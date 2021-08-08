<?php

system('clear');

$harFiles = glob("./*.har");

foreach ($harFiles as $harFile) {
    $fileInfo = pathinfo($harFile);
    $harFileDir = './' . $fileInfo['filename'];

    if (is_dir($harFileDir) === false) {
        mkdir($harFileDir);
    }

    $harContent = json_decode(file_get_contents($harFile), true);

    $all = count($harContent['log']['entries']);
    $cc = 1;

    foreach ($harContent['log']['entries'] as $entry) {
        if (isset($entry['response']['content']['mimeType'])) {
            if ($entry['response']['content']['mimeType'] === 'image/jpeg') {
                $randFileName = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

                    // 32 bits for "time_low"
                    mt_rand(0, 0xffff), mt_rand(0, 0xffff),

                    // 16 bits for "time_mid"
                    mt_rand(0, 0xffff),

                    // 16 bits for "time_hi_and_version",
                    // four most significant bits holds version number 4
                    mt_rand(0, 0x0fff) | 0x4000,

                    // 16 bits, 8 bits for "clk_seq_hi_res",
                    // 8 bits for "clk_seq_low",
                    // two most significant bits holds zero and one for variant DCE1.1
                    mt_rand(0, 0x3fff) | 0x8000,

                    // 48 bits for "node"
                    mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
                );

                $newFileName = $harFileDir . '/' . $randFileName . '.jpg';

                echo $newFileName . " ({$cc}/{$all})" . PHP_EOL;

                $fileContent = $entry['response']['content']['text'];
                file_put_contents($newFileName, base64_decode($fileContent));
                $cc++;
            } else {
                echo "Skip ({$cc}/{$all})" . PHP_EOL;
                $cc++;
            }
        } else {
                echo "Skip ({$cc}/{$all})" . PHP_EOL;
                $cc++;
        }
    }
    
    echo PHP_EOL;
}


