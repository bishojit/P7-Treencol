<?php

namespace Packages\tools\develop\models;

class DevHtmlHead
{
    private $pageTitle;

    function setTitle($title)
    {
        $this->pageTitle = $title;
        return $this;
    }

    function getHtml()
    {
        return "
        <title>$this->pageTitle</title>
        <meta name=\"Description\" content=\"Bikiran, A software development company in Bangladesh.\"/>
    
        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
        <meta id=\"extViewportMeta\" name=\"viewport\"
              content=\"width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no\">
        <meta name=\"apple-mobile-web-app-capable\" content=\"yes\">
        <meta name=\"apple-mobile-web-app-status-bar-style\" content=\"black\"/>
    
        <meta property=\"og:description\" content=\"Bikiran, A software development company in Bangladesh.\"/>
        <meta property=\"og:locale\" content=\"en_US\"/>
        <meta property=\"og:title\" content=\"Users\"/>
        <meta property=\"og:type\" content=\"service\"/>
        <meta property=\"og:url\" content=\"https://treencol.com/client/users/\"/>
        <meta property=\"og:site_name\" content=\"Bikitan Portal\"/>
    
        <link href=\"//cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css\" rel=\"stylesheet\" type=\"text/css\"/>
        <link href=\"//stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css\" rel=\"stylesheet\" type=\"text/css\"/>
        <link href=\"//use.fontawesome.com/releases/v5.5.0/css/all.css\" rel=\"stylesheet\" type=\"text/css\"/>
        <link href=\"//unpkg.com/ionicons@4.4.4/dist/css/ionicons.min.css\" rel=\"stylesheet\" type=\"text/css\"/>
        <link href=\"//cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css\" rel=\"stylesheet\"
              type=\"text/css\"/>
        <link href=\"//fonts.googleapis.com/css?family=Open+Sans:400,600,700,800\" rel=\"stylesheet\" type=\"text/css\"/>
        <link href=\"//cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css\" rel=\"stylesheet\" type=\"text/css\"/>
        <link href=\"//treencol.com/assets/tooltipster/css/tooltipster.bundle.min.css\" rel=\"stylesheet\"
              type=\"text/css\"/>
        <link href=\"//treencol.com/assets/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-punk.min.css\"
              rel=\"stylesheet\" type=\"text/css\"/>
        <link href=\"//treencol.com/assets/css/bik-8.0.0.js.css\" rel=\"stylesheet\" type=\"text/css\"/>
        <link href=\"//treencol.com/assets/css/style.css\" rel=\"stylesheet\" type=\"text/css\"/>
    
        <script src=\"/cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js\" type=\"text/javascript\"></script>
        <script src=\"/cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js\" type=\"text/javascript\"></script>
        <script src=\"/cdn.jsdelivr.net/npm/flatpickr\" type=\"text/javascript\"></script>
        <script src=\"/cdn.jsdelivr.net/npm/shortcut-buttons-flatpickr@0.1.0/dist/shortcut-buttons-flatpickr.min.js\"
                type=\"text/javascript\"></script>
        <script src=\"/treencol.com/assets/tooltipster/js/tooltipster.bundle.min.js\" type=\"text/javascript\"></script>
        <script src=\"/treencol.com/assets/js/bik-8.0.0.js\" type=\"text/javascript\"></script>
        <script src=\"/treencol.com/assets/js/main.js\" type=\"text/javascript\"></script>
        <script src=\"/treencol.com/assets/js/script.js\" type=\"text/javascript\"></script>
        ";
    }
}