
<?php

$zip = new ZipArchive();

$file = getenv('USERPROFILE') . '/Downloads/BASIRI SALI KHAN.zip';

echo "Checking: " . $file . PHP_EOL;

if (!file_exists($file)) {
    die("ERROR: ZIP FILE NOT FOUND");
}

$result = $zip->open($file, ZipArchive::CHECKCONS);

var_dump($result);

if ($result === true) {
    echo "ZIP IS VALID" . PHP_EOL;

    echo "Files inside ZIP:" . PHP_EOL;

    for ($i = 0; $i < $zip->numFiles; $i++) {
        echo $zip->getNameIndex($i) . PHP_EOL;
    }

    $zip->close();
} else {
    echo "ZIP IS INVALID" . PHP_EOL;
}
