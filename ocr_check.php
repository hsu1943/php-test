<?php
# 数据核对修正完成后将前面的图片标识去掉

$dir = 'files/用药证明/医嘱/20240722';
$result_file = $dir . '/result.txt';
$final_file = $dir . '/final-0722.txt';

$content = file_get_contents($result_file);
$arr = explode(PHP_EOL, $content);
$res = [];
foreach ($arr as $line) {
    if (trim(empty($line))) {
        continue;
    }
    file_put_contents($final_file, trim(explode('|', $line)[1]) . PHP_EOL, FILE_APPEND);
}
