<?php
// 文字识别，保存结果

function post($url, $headers, $body)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

// 通用文字识别
const URL = 'http://aicheck-api1.papv2.sungotech.com';
// const URL = 'http://api.aicheck.test';

require_once 'keys.php';
$ts = (string)(time());

$headers = [
    'Project-Name: ' . APP_KEY,
    'Ts: ' . $ts,
    'Signature: ' .  md5(APP_KEY . APP_SECRET . $ts)
];

$url = URL . '/ocr/index';
$dir = 'files/用药证明/注射单_输液单_输注证明/20240717';
$result_file = $dir . '/result.txt';

$files = scandir($dir);
foreach ($files as $file) {
    if (in_array($file, ['.', '..'])) {
        continue;
    }
    if (strpos($file, '.txt') === false) {
        $file_path = $dir . '/' . $file;
        $file_exists = file_get_contents($result_file);
        if (strpos($file_exists, $file) !== false) {
            continue;
        }
        echo $file_path . PHP_EOL;
        $content = file_get_contents($file_path);
        if (empty($content)) {
            continue;
        }

        $postData = [
            'file'  =>  base64_encode($content),
            'version' => 'v2'
        ];

        $response = post($url, $headers, $postData);
        $ocrResult = json_decode($response, true);
        if ($ocrResult['status']) {
            $str = $file . ' | ' . $ocrResult['data'][0] . PHP_EOL;
            file_put_contents($result_file, $str, FILE_APPEND);
        }
    }
    break;
}
