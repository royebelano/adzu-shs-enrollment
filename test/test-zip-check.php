<?php

$zip = new ZipArchive();

$file = getenv('USERPROFILE') . '/Downloads/BASIRI SALI KHAN.zip';

echo "Checking: $file\n";

if (!file_exists($file)) {
    die("ZIP FILE NOT FOUND\n");
}

$result = $zip->open($file, ZipArchive::CHECKCONS);

var_dump($result);

if ($result === true) {
    echo "ZIP IS VALID\n";

    for ($i = 0; $i < $zip->numFiles; $i++) {
        echo $zip->getNameIndex($i) . "\n";
    }

    $zip->close();
} else {
    echo "ZIP IS INVALID\n";
}