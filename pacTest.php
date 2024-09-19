<?php
function pacOriginal($arr): array
{
    if (empty($arr)) {
        return [];
    }

    $n = count($arr);   // 维度数量
    $count = 1;         // 结果总数
    $size = [];         // 每个属性的可选值个数
    $visit = [];        // 每个属性当前访问到的位置（下标）
    $keyIndex = [];     // 保存每个属性的key
    $res = [];          // 保存结果

    // 初始化
    $i = 0;
    foreach ($arr as $key => $values) {
        $size[$i] = count($values);
        $visit[$i] = 0;
        $count *= count($values);
        $keyIndex[$i] = $key;
        $i ++;
    }

    $m = 0;
    $temp = [];
    while (true) {
        for ($i = 0; $i < $n; $i++) {
            $temp[$m][$i] = $visit[$i] + 1;
        }
        $m ++;
        for ($i = $n - 1; $i >= 0; $i--) {
            if ($visit[$i] == $size[$i] - 1) {
                $visit[$i] = 0;
            } else {
                break;
            }
        }
        if ($i < 0) {
            break;
        }
        $visit[$i] ++;
    }

    for ($i = 0; $i < $count; $i ++) {
        for ($j = 0; $j < $n; $j ++) {
            $res[$i][$keyIndex[$j]] = $arr[$keyIndex[$j]][$temp[$i][$j]-1];
        }
    }

    return $res;
}

function pacOptimized($arr): array
{
    if (empty($arr)) {
        return [];
    }

    $result = [];
    $keys = array_keys($arr);
    $values = array_values($arr);

    // 使用闭包实现递归
    $cartesianProduct = function ($values, $keys, $index = 0, $current = []) use (&$result, &$cartesianProduct) {
        if ($index == count($values)) {
            $result[] = $current;
            return;
        }

        foreach ($values[$index] as $value) {
            $current[$keys[$index]] = $value;
            $cartesianProduct($values, $keys, $index + 1, $current);
        }
    };

    $cartesianProduct($values, $keys);
    return $result;
}

function pacGenerator($arr)
{
    if (empty($arr)) {
        yield [];
        return;
    }

    $keys = array_keys($arr);
    $values = array_values($arr);

    // 使用生成器实现递归
    $cartesianProduct = function ($values, $keys, $index = 0, $current = []) use (&$cartesianProduct) {
        if ($index == count($values)) {
            yield $current;
            return;
        }

        foreach ($values[$index] as $value) {
            $current[$keys[$index]] = $value;
            yield from $cartesianProduct($values, $keys, $index + 1, $current);
        }
    };

    yield from $cartesianProduct($values, $keys);
}

// 测试数据
$arr = [
    'color' => ['red', 'blue', 'green', 'yellow', 'black', 'white', 'gray', 'pink', 'orange', 'purple'],
    'size' => ['S', 'M', 'L', 'XL', 'XXL', 'XXXL'],
    'material' => ['cotton', 'wool', 'silk', 'polyester', 'rayon', 'acrylic', 'nylon', 'spandex'],
    'brand' => ['Nike', 'Adidas', 'Puma', 'Reebok', 'Under Armour', 'New Balance', 'Asics'],
    'style' => ['Casual', 'Formal', 'Sport', 'Vintage', 'Modern', 'Classic', 'Trendy'],
    // 'gender' => ['Men', 'Women', 'Unisex'],
    // 'season' => ['Spring', 'Summer', 'Autumn', 'Winter'],
    // 'country' => ['USA', 'China', 'India', 'Japan', 'Germany', 'France', 'Italy', 'UK'],
    // 'price' => ['Low', 'Medium', 'High'],
    // 'rating' => ['1', '2', '3', '4', '5']
];

// 原始代码测试
$startOriginal = microtime(true);
$resultOriginal = pacOriginal($arr);
$endOriginal = microtime(true);
$timeOriginal = $endOriginal - $startOriginal;
$memoryOriginal = memory_get_peak_usage(true);

// 优化后代码测试
$startOptimized = microtime(true);
$resultOptimized = pacOptimized($arr);
$endOptimized = microtime(true);
$timeOptimized = $endOptimized - $startOptimized;
$memoryOptimized = memory_get_peak_usage(true);

// 生成器代码测试
$startGenerator = microtime(true);
$resultGenerator = [];
foreach (pacGenerator($arr) as $combination) {
    $resultGenerator[] = $combination;
}
$endGenerator = microtime(true);
$timeGenerator = $endGenerator - $startGenerator;
$memoryGenerator = memory_get_peak_usage(true);

// 输出结果
echo "Original Code:\n";
echo "Execution Time: " . $timeOriginal . " seconds\n";
echo "Memory Usage: " . $memoryOriginal . " bytes\n\n";

echo "Optimized Code:\n";
echo "Execution Time: " . $timeOptimized . " seconds\n";
echo "Memory Usage: " . $memoryOptimized . " bytes\n\n";

echo "Generator Code:\n";
echo "Execution Time: " . $timeGenerator . " seconds\n";
echo "Memory Usage: " . $memoryGenerator . " bytes\n";
