<?php

namespace Packages\send;

use Packages\mysql\QuerySelect;
use Packages\mysql\QueryUpdate;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class EmailSend
{
    /**
     * @var PHPMailer
     */
    private $mail;
    /**
     * @var EmailTemplates
     */
    private $EmailTemplates;

    private $Host = "";
    private $Username = "";
    private $Password = "";
    private $Port = "";
    private $group = "default";
    private $message = "";
    private $FromEmail = "";
    private $FromName = "";
    private $ReplyToEmail = "";
    private $ReplyToName = "";


    function __construct($templatePath = "app/system/email-templates/email_template_default.html")
    {
        $path = "configs/access/" . getDefaultDomain() . "/" . getDefaultDomain() . ".email.xml";
        $xmlEmailConfig = xmlFileToObject($path, "Email Configuration File Not Found");


        $this->Host = (string)$xmlEmailConfig->{$this->group}->Host;
        $this->Username = (string)$xmlEmailConfig->{$this->group}->Username;
        $this->Password = (string)$xmlEmailConfig->{$this->group}->Password;
        $this->Port = (string)$xmlEmailConfig->{$this->group}->Port;

        $this->FromEmail = (string)$xmlEmailConfig->{$this->group}->FromEmail;
        $this->FromName = (string)$xmlEmailConfig->{$this->group}->FromName;

        $this->ReplyToEmail = (string)$xmlEmailConfig->{$this->group}->ReplyToEmail;
        $this->ReplyToName = (string)$xmlEmailConfig->{$this->group}->ReplyToName;

        // Instantiation and passing `true` enables exceptions
        $this->mail = new PHPMailer(true);
        $this->EmailTemplates = new EmailTemplates($templatePath);
    }

    function PHPMailer(): PHPMailer
    {
        return $this->mail;
    }

    function EmailTemplates(): EmailTemplates
    {
        return $this->EmailTemplates;
    }

    function send()
    {
        //Server settings
        $this->mail->SMTPDebug = 0; //SMTP::DEBUG_SERVER;         // Enable verbose debug output
        $this->mail->isSMTP();                                    // Send using SMTP
        $this->mail->Host = $this->Host;                          // Set the SMTP server to send through
        $this->mail->SMTPAuth = true;                             // Enable SMTP authentication
        $this->mail->Username = $this->Username;                  // SMTP username
        $this->mail->Password = $this->Password;                  // SMTP password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $this->mail->Port = $this->Port;                          // TCP port to connect to

        try {
            //Recipients
            $this->mail->setFrom($this->FromEmail, $this->FromName);
            $this->mail->addReplyTo($this->ReplyToEmail, $this->ReplyToName);

            // Content
            $this->mail->isHTML(true);                                  // Set email format to HTML
            $this->mail->Subject = $this->EmailTemplates->getSubject();
            $this->mail->Body = $this->EmailTemplates->getHtml();

            $text = $this->EmailTemplates->getBodyText();
            if ($text) {
                $this->mail->AltBody = $this->EmailTemplates->getBodyText();
            }

            $this->mail->send();
            $this->message = 'Message has been sent';
        } catch (Exception $e) {
            $this->message = "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
        }
    }

    public function setGroup(string $group): self
    {
        $this->group = $group;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    function sendPendingEmail($extraData_ar = [])
    {
        $select = new QuerySelect("notification_messages");
        $select->setQueryString("
        SELECT * 
        FROM `notification_messages` 
        WHERE `type`='email'
            AND `time_sent`=0
            AND `time_schedule`<=" . getTime() . "
        ORDER BY `sl` DESC
        LIMIT 1
        ");
        $select->pull();
        $Info_ar = $select->getRow();
        $parameters_ar = json_decode($Info_ar['parameters_json'], true) ?: [];

        //--Data Mixing
        $parameters_ar['domain'] = getDefaultDomain();
        foreach ($extraData_ar as $key => $val) {
            $parameters_ar[$key] = $val;
        }

        //--Sending Email
        if ($Info_ar['template_path']) {
            $this->EmailTemplates->setTemplatePath(str_replace("{domain}", getDefaultDomain(), $Info_ar['template_path']));
        }
        $this->PHPMailer()->addAddress($Info_ar['receiver_contact']);
        $this->EmailTemplates()->setSubject($Info_ar['subject']);
        $this->EmailTemplates()->addValueAr($parameters_ar);
        $this->send();

        //--Data Updater
        $update = new QueryUpdate('notification_messages');
        $update->updateRow($Info_ar, [
            'time_sent' => getTime(),
            'body' => $this->mail->Body
        ]);
        $update->push();
    }
}