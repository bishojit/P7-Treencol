<?php
// List of your files


function getDirJson(string $dir): string
{
    $excludeList_ar = [
        //--Dir
        '.' => true,
        '..' => true,
        'cloud-uploads' => true,
        'app' => true,

        //--Files
        '.user.ini' => true,
        '.treen.php' => true,
        'composer.lock' => true,
        'favicon.ico' => true,
        'test.html' => true,
        'test.json' => true,
        'todo.txt' => true,
    ];

    $list_ar = scandir($dir);

    $file_ar = [];
    $dir_ar = [];
    foreach ($list_ar as $file) {
        if (!$excludeList_ar[$file]) {
            $path = $dir . "/" . $file;
            if (is_file($path)) {
                $file_ar[] = $path;
            }
            if (is_dir($path)) {
                $dir_ar[] = $path;
            }
        }
    }

    return json_encode([
        'dir' => $dir_ar,
        'file' => $file_ar,
    ], JSON_FORCE_OBJECT);
}

function getFile($file)
{
    return json_encode([
        'code' => is_file($file) ? file_get_contents($file) : "",
    ], JSON_FORCE_OBJECT);
}

if ($_GET['listPath']) {
    echo getDirJson($_GET['listPath']);
}


if ($_GET['filePath']) {
    echo getFile($_GET['filePath']);
}

echo "\n";
