<?php


namespace Packages\treen;


class TreenProcess
{
    private array $failed_ar = [];
    private array $success_ar = [];
    private TreenColors $TreenColors;
    private array $unWantedAr = [
        'core/Test.php',
        'core/TreenColors.php',
        'core/TreenProcess.php',
        'packages/bikiran/Event.php',
    ];

    private array $appFilesAr = [
        'app/_defaults/default_pages/error_config_html.php',
        'app/_defaults/default_pages/error_login_html.php',
        'app/_defaults/default_pages/error_method_html.php',
        'app/_defaults/default_pages/error_page_html.php',
    ];

    private array $configFilesAr = [
        'configs/domain-mapping.xml',
        'configs/history-disable.xml',
        'configs/system-defaults.xml',
    ];

    function __construct()
    {
        $this->TreenColors = new TreenColors();
    }

    function display($message, $color = "", $bg = "")
    {
        $message = date("Y-m-d H:i:s A") . ": " . $message . "\n";
        echo $this->TreenColors->getColoredString($message, $color, $bg);
        usleep(__DELAY_MS__);
    }

    function setupEnvironment()
    {
        error_reporting(E_ALL & ~E_NOTICE);
        ini_set('display_errors', 1);
    }

    function process($argv)
    {
        //--Starting Log
        $this->display("Start", "blue");

        $upgrade = in_array('--upgrade', $argv);
        if ($upgrade) {

            //--Copy App Files
            $this->createAppFiles();

            //--Copy .gitignore
            $this->copyGitIgnore();

            //--Detect & Process CLI Values
            $this->setupEnvironment();

            $this->upgrade();

            //--Remove Unwanted Files
            $this->removeUnwantedFiles();

            //--Log
            $this->display("Complete, Total Success:" . count($this->getSuccessAr()) . ", Total Failed:" . count($this->getFailedAr()), empty($this->getFailedAr()) ? "blue" : "red");
        }

        $module = in_array('--module', $argv);
        if ($module) {
            TreenModules::argv($argv, $this);
            exit();
        }


        /*$upgradeCore = in_array('-core', $argv);

        $update = in_array('--update', $argv);
        $updateCache = in_array('-cache', $argv); //--Update Cache

        $check = in_array('--check', $argv);
        $force = in_array('--force', $argv);*/

        $help = in_array('--help', $argv);

        if ($help) {
            $this->displayHelp();
            exit(0);
        }
    }

    function displayHelp()
    {
        echo <<<EOF
Composer Installer
------------------
Options
--help               this help
--update             Its Update All(cache)
--update -cache       Its Update Cache Only

--upgrade                       Its Update All(core, packages)
--upgrade -dir:core             Its Update core Files Only
--upgrade -dir:packages         Its Update packages Files Only
--upgrade -dir:assets/treencol  
 

EOF;
    }


    function curlGetData($url) // $type=file|list
    {
        set_time_limit(0);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $content = curl_exec($ch);
        $error = curl_error($ch);
        if ($error) {
            $this->display($error, "red", "black");
        }
        curl_close($ch);


        return $content;
    }

    function getFileData($filePath)
    {
        $data = $this->curlGetData(__MOTHER_LINK__ . "?filePath=" . $filePath);
        if ($data) {
            $this->success_ar[] = $filePath;
            //$this->display("File Loaded Success (" . $filePath . ")");
        } else {
            $this->failed_ar[] = $filePath;
            $this->display("File Loaded Failed (" . $filePath . ")", "red", "black");
        }
        return json_decode($data, true)['code'];
    }

    function getListData($dirPath)
    {
        $data = $this->curlGetData(__MOTHER_LINK__ . "?listPath=" . $dirPath);
        if ($data) {
            $this->success_ar[] = $dirPath;
            $this->display("List Loaded Success (" . $dirPath . ")", "blue");
        } else {
            $this->failed_ar[] = $dirPath;
            $this->display("List Loaded Failed (" . $dirPath . ")", "red", "black");
        }
        return json_decode($data, true);
    }

    function dirPath($dirPath)
    {
        $list_ar = $this->getListData($dirPath);

        foreach ($list_ar['dir'] ?: [] as $dirPath) {
            $this->dirPath($dirPath);
        }

        foreach ($list_ar['file'] ?: [] as $filePath) {
            $this->filePath($filePath);
        }
    }

    function filePath($filePath)
    {
        $code = $this->getFileData($filePath);

        if ($code) {
            if (!is_file($filePath)) {
                $this->file_put_contents_force($filePath, $code);
                $this->display("Created ($filePath) [New File]", "blue");
            } else {
                if (md5($code) != md5(file_get_contents($filePath))) {
                    $this->file_put_contents_force($filePath, $code);
                    $this->display("Replaced ($filePath) [File Updated]", "blue");
                } else {
                    $this->display("Skipped ($filePath) [No Change]", "blue");
                }
            }
        }
    }

    function file_put_contents_force($filePath, $code)
    {
        $dir = pathinfo($filePath, PATHINFO_DIRNAME);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($filePath, $code);
    }

    function upgrade($dir_ar = [])
    {
        $package_ar = [];
        $package_ar['core'] = 'core';
        $package_ar['packages'] = 'packages';

        if (!$dir_ar) {
            $dir_ar = array_keys($package_ar);
        }

        foreach ($dir_ar ?: [] as $dir) {
            $this->dirPath($dir);
        }

        $file_ar = [];
        $file_ar['index.php'] = 'index.php';
        $file_ar['treen'] = 'treen';
        $file_ar['treen.php'] = 'treen.php';

        foreach ($file_ar ?: [] as $file) {
            $this->filePath($file);
        }
    }

    //--Create APP Files
    function createAppFiles()
    {
        foreach ($this->appFilesAr as $filePath) {
            if (!file_exists($filePath)) {
                $this->filePath($filePath);
            } else {
                $this->display("Skipped ($filePath) [No Change]", "blue");
            }
        }
    }

    //--Create Config Files
    function createConfigFiles()
    {
        foreach ($this->configFilesAr as $filePath) {
            if (!file_exists($filePath)) {
                $this->filePath($filePath);
            } else {
                $this->display("Skipped ($filePath) [No Change]", "blue");
            }
        }
    }

    //--Copy .gitignore
    public function copyGitIgnore()
    {
        $filePath = ".gitignore";

        //--Collect Remote gitignore file
        $gitIgnoreRemoteData = $this->getFileData($filePath);
        $remoteGitIgnore_ar = array_flip(explode("\n", $gitIgnoreRemoteData));

        //--Collect Local gitignore file
        $gitIgnoreLocalData = null;
        $localGitIgnore_ar = [];
        if (is_file($filePath)) {
            $gitIgnoreLocalData = file_get_contents($filePath);
            $localGitIgnore_ar = array_flip(explode("\n", $gitIgnoreLocalData));
        }

        if ($gitIgnoreRemoteData) {
            if (!is_file($filePath)) {
                $lines_ar = $remoteGitIgnore_ar + $localGitIgnore_ar;
                $this->file_put_contents_force(".gitignore", implode("\n", array_keys($lines_ar)));

                $this->display("Created ($filePath) [New File]", "blue");
            } else {
                if (md5($gitIgnoreRemoteData) != md5(file_get_contents($filePath))) {
                    $lines_ar = $remoteGitIgnore_ar + $localGitIgnore_ar;
                    $this->file_put_contents_force(".gitignore", implode("\n", array_keys($lines_ar)));

                    $this->display("Replaced ($filePath) [File Updated]", "blue");
                } else {
                    $this->display("Skipped ($filePath) [No Change]", "blue");
                }
            }
        }
    }

    //--Remove Unwanted Files
    function removeUnwantedFiles()
    {
        foreach ($this->unWantedAr as $file) {
            if (file_exists($file)) {
                unlink($file);
                $this->display("Deleted ($file) [File Removed]", "red");
            }
        }
    }

    public function getFailedAr(): array
    {
        return $this->failed_ar;
    }

    public function getSuccessAr(): array
    {
        return $this->success_ar;
    }
}