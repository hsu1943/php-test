<?php


$arr = ['wx_debug' => '2222'];
$a = !!($_COOKIE['wx_debug'] ?? '');
var_dump($a);
die();

// $a = 2 * 3 - 1;
// $b = 2 - 1 * 3;
// echo $a . PHP_EOL;
// echo $b . PHP_EOL;

// $c = '-1';

// var_dump($c == $b);


$c = ['加', '减', '乘'];

$ca = [
    '加' => '+',
    '减' => '-',
    '乘' => '*'
];

$keys = array_rand($c, 2);
while (!in_array(2, $keys)) {
    $keys = array_rand($c, 2);
}

$c1_k = array_rand($keys);
$c1 = $c[$keys[$c1_k]];
unset($keys[$c1_k]);
$c2_k = array_values($keys)[0];
$c2 = $c[$c2_k];


// 随机取三个数字
$num1 = mt_rand(1, 20);
$num2 = mt_rand(1, 20);
$num3 = mt_rand(1, 20);

// 随机取一个数字与$c1组成乘法运算式
$expr = "{$num1} {$ca[$c1]} {$num2} {$ca[$c2]} {$num3}";

// 计算结果
$result = eval("return {$expr};");

// 如果结果超出范围，则重新生成数学计算式
while ($result < -10 || $result > 10) {
    $num1 = mt_rand(1, 20);
    $num2 = mt_rand(1, 20);
    $num3 = mt_rand(1, 20);
    $expr = "{$num1} {$ca[$c1]} {$num2} {$ca[$c2]} {$num3}";
    $result = eval("return {$expr};");
}

$cn_expr = "{$num1} {$c1} {$num2} {$c2} {$num3}";
echo "中文算式：{$cn_expr}\n";
echo "数学计算式：{$expr}\n";
echo "计算结果：{$result}\n";
echo "-----------------------------------\n";
