<?php
/**
 * 测试下载并保存文件
 */
require 'vendor/autoload.php';

// 使用files目录下的urls.txt 文件中的连接，下载文件到files目录
$urls = file_get_contents('files/用药证明/urls.txt');
$urls = explode("\n", $urls);
foreach ($urls as $url) {
    $url = trim($url);
    if (empty($url)) {
        continue;
    }
    $fileName = basename($url);
    $filePath = 'files/用药证明/download/' . $fileName;
    // echo $filePath . PHP_EOL;
    if (file_exists($filePath)) {
        echo "文件 $fileName 已存在，跳过下载\n";
        continue;
    }
    echo "开始下载 $fileName\n";
    try {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $url);
        $content = $response->getBody()->getContents();
        file_put_contents($filePath, $content);
        echo "下载 $fileName 成功\n";
    } catch (\Exception $e) {
        echo "下载 $fileName 失败\n" . $e->getMessage();
    }
    // break;
}
