<?php
// 文字识别，保存结果

// goto test;
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

$url = URL . '/ocr/table';
$dir = 'files/用药证明/注射单_输液单_输注证明/20240717';
$result_file = $dir . '/result.txt';

$files = scandir($dir);
$i = 0;
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
            $plain_text = get_table_text($ocrResult);
            echo $plain_text . PHP_EOL;
            $str = $file . ' | ' . $plain_text . PHP_EOL;
            file_put_contents($result_file, $str, FILE_APPEND);
        }
    }
    // break;
    // $i++;
    // if ($i > 20) {
    //     break;
    // }
}


# 注射单的特殊处理情况
function get_table_text($result)
{
    $data = $result['data'][0];
    $plain_text = $data['plain_text'] ?? '';
    $plain_text_no_blank = str_replace(' ', '', $plain_text);
    $tables = $data['tables'] ?? [];
    $header = [];
    foreach ($tables as $table) {
        if (!empty($table['header'])) {
            $header = $table['header'];
        }
        $rows = $table['rows'] ?? [];
        foreach ($rows as $row) {
            $row_text = '';
            $none_cell_count = 0;
            foreach ($row as $cell) {
                if (empty($cell)) {
                    $none_cell_count++;
                } else {
                    $row_text .= $cell . ' ';
                }
            }

            if (empty($header)) {
                if (strpos($plain_text_no_blank, str_replace(' ', '', $row_text)) === false) {
                    $plain_text .= ' ' . $row_text;
                    $plain_text_no_blank = str_replace(' ', '', $plain_text);
                }
            } else {
                // echo json_encode($row, 320) . PHP_EOL;
                // echo $none_cell_count . " | " . count($row) . " | " . (int)(count($row) / 2) . PHP_EOL;
                if ($none_cell_count >= (int)(count($row) / 2)) {
                    if (strpos($plain_text_no_blank, str_replace(' ', '', $row_text)) === false) {
                        $plain_text .= ' ' . $row_text;
                        $plain_text_no_blank = str_replace(' ', '', $plain_text);
                    }
                } else {
                    $row_text = '';
                    foreach ($header as $key => $value) {
                        if (!empty($row[$key])) {
                            $row_text .= $value . ':' . $row[$key] . ' ';
                        }
                    }
                    $plain_text .= ' ' . $row_text;
                    $plain_text_no_blank = str_replace(' ', '', $plain_text);
                }
            }
        }
    }

    return $plain_text;
}


// test:

// // $str = '{"status": true, "msg": "success", "data": [{"plain_text": "注: 医生签名(盖章): 符勇 如需转就近医院注射,请至门诊办必室, 盖章有效 海军军医大学第三附属医院 注射单 姓名: 姚磊 性别: 男 年龄: 40 2024 年 7 月 15 日", "tables": [{"type": "table_with_line", "table_rows": 2, "table_cols": 6, "header": [], "rows": [["海军军医大学第三附属医院 注射单 姓名: 姚磊 性别: 男 年龄: 40 2024 年 7 月 15 日", "", "", "", "", ""], ["肌注", "皮", "静 注", "静滴", "过敏试验 要(免", "护士签名:"]]}]}], "raw": {"code": 200, "message": "success", "duration": 821, "result": {"angle": 0, "height": 4032, "tables": [{"height_of_rows": [1898, 645], "type": "table_with_line", "table_cells": [{"end_row": 0, "borders": {"right": 1, "bottom": 1, "left": 1, "top": 1}, "position": [497, 284, 2658, 317, 2642, 1377, 481, 1344], "end_col": 5, "start_row": 0, "start_col": 0, "lines": [{"angle": 0, "text": "海军军医大学第三附属医院", "direction": 1, "handwritten": 0, "position": [824, 395, 2352, 405, 2352, 557, 824, 549], "score": 0.999, "type": "text"}, {"angle": 0, "text": "注射单", "direction": 1, "handwritten": 0, "position": [1184, 613, 1988, 598, 1992, 776, 1188, 790], "score": 0.999, "type": "text"}, {"angle": 0, "text": "姓名:", "direction": 1, "handwritten": 0, "position": [533, 982, 784, 981, 784, 1104, 533, 1105], "score": 0.999, "type": "text"}, {"angle": 0, "text": "姚磊", "direction": 1, "handwritten": 1, "position": [948, 921, 1401, 908, 1407, 1118, 956, 1132], "score": 0.996, "type": "text"}, {"angle": 0, "text": "性别:", "direction": 1, "handwritten": 0, "position": [1723, 986, 1971, 986, 1972, 1107, 1723, 1107], "score": 0.999, "type": "text"}, {"angle": 0, "text": "男", "direction": 0, "handwritten": 1, "position": [2110, 880, 2336, 879, 2337, 1119, 2108, 1120], "score": 0.996, "type": "text"}, {"angle": 0, "text": "年龄:", "direction": 1, "handwritten": 0, "position": [532, 1204, 785, 1212, 781, 1333, 528, 1324], "score": 0.999, "type": "text"}, {"angle": 0, "text": "40 2024", "direction": 1, "handwritten": 1, "position": [898, 1133, 1684, 1145, 1680, 1438, 894, 1424], "score": 0.97, "type": "text"}, {"angle": 0, "text": "年", "direction": 0, "handwritten": 0, "position": [1710, 1215, 1829, 1214, 1829, 1333, 1711, 1334], "score": 0.999, "type": "text"}, {"angle": 0, "text": "7", "direction": 0, "handwritten": 0, "position": [1841, 1163, 2029, 1164, 2030, 1376, 1840, 1376], "score": 0.805, "type": "text"}, {"angle": 0, "text": "月", "direction": 0, "handwritten": 0, "position": [2037, 1211, 2161, 1210, 2161, 1336, 2038, 1336], "score": 0.965, "type": "text"}, {"angle": 0, "text": "15", "direction": 1, "handwritten": 1, "position": [2152, 1174, 2365, 1176, 2365, 1348, 2152, 1347], "score": 0.97, "type": "text"}, {"angle": 0, "text": "日", "direction": 0, "handwritten": 0, "position": [2385, 1207, 2485, 1206, 2486, 1330, 2385, 1328], "score": 0.997, "type": "text"}], "text": "海军军医大学第三附属医院\n注射单\n姓名: 姚磊 性别: 男\n年龄: 40 2024 年 7 月 15 日"}, {"end_row": 1, "borders": {"right": 1, "bottom": 1, "left": 1, "top": 1}, "position": [497, 2330, 685, 2333, 677, 2834, 489, 2832], "end_col": 0, "start_row": 1, "start_col": 0, "lines": [{"angle": 0, "text": "肌注", "direction": 2, "handwritten": 0, "position": [518, 2393, 664, 2395, 660, 2780, 512, 2778], "score": 0.963, "type": "text"}], "text": "肌注"}, {"end_row": 1, "borders": {"right": 1, "bottom": 1, "left": 1, "top": 1}, "position": [685, 2330, 869, 2333, 861, 2837, 677, 2834], "end_col": 1, "start_row": 1, "start_col": 1, "lines": [{"angle": 0, "text": "皮", "direction": 0, "handwritten": 0, "position": [711, 2407, 836, 2409, 836, 2546, 711, 2546], "score": 0.999, "type": "text"}], "text": "皮"}, {"end_row": 1, "borders": {"right": 1, "bottom": 1, "left": 1, "top": 1}, "position": [869, 2331, 1050, 2334, 1042, 2840, 861, 2837], "end_col": 2, "start_row": 1, "start_col": 2, "lines": [{"angle": 0, "text": "静", "direction": 0, "handwritten": 0, "position": [877, 2401, 1018, 2401, 1018, 2542, 877, 2542], "score": 0.998, "type": "text"}, {"angle": 0, "text": "注", "direction": 0, "handwritten": 0, "position": [884, 2631, 1021, 2631, 1021, 2781, 884, 2782], "score": 0.999, "type": "text"}], "text": "静\n注"}, {"end_row": 1, "borders": {"right": 1, "bottom": 1, "left": 1, "top": 1}, "position": [1050, 2334, 1236, 2337, 1228, 2843, 1042, 2840], "end_col": 3, "start_row": 1, "start_col": 3, "lines": [{"angle": 0, "text": "静滴", "direction": 2, "handwritten": 0, "position": [1067, 2401, 1208, 2401, 1207, 2782, 1066, 2782], "score": 0.939, "type": "text"}], "text": "静滴"}, {"end_row": 1, "borders": {"right": 1, "bottom": 1, "left": 1, "top": 1}, "position": [1357, 2341, 1941, 2350, 1934, 2854, 1349, 2845], "end_col": 4, "start_row": 1, "start_col": 4, "lines": [{"angle": 0, "text": "过敏试验", "direction": 1, "handwritten": 0, "position": [1414, 2402, 1878, 2414, 1876, 2557, 1410, 2545], "score": 0.996, "type": "text"}, {"angle": 0, "text": "要(免", "direction": 1, "handwritten": 0, "position": [1467, 2647, 1794, 2648, 1794, 2791, 1467, 2790], "score": 0.878, "type": "text"}], "text": "过敏试验\n要(免"}, {"end_row": 1, "borders": {"right": 1, "bottom": 1, "left": 1, "top": 1}, "position": [1941, 2350, 2634, 2360, 2626, 2869, 1934, 2858], "end_col": 5, "start_row": 1, "start_col": 5, "lines": [{"angle": 0, "text": "护士签名:", "direction": 1, "handwritten": 0, "position": [1970, 2404, 2494, 2403, 2494, 2549, 1970, 2549], "score": 0.998, "type": "text"}], "text": "护士签名:"}], "table_rows": 2, "width_of_cols": [190, 184, 181, 247, 644, 703], "position": [497, 284, 2665, 318, 2626, 2869, 458, 2836], "lines": [], "table_cols": 6}, {"height_of_rows": [], "type": "plain", "table_cells": [], "table_rows": 0, "width_of_cols": [], "position": [0, 0, 3023, 1, 3022, 4031, 0, 4031], "lines": [{"angle": 0, "text": "注:", "direction": 1, "handwritten": 0, "position": [455, 3140, 628, 3141, 628, 3282, 455, 3281], "score": 0.999, "type": "text"}, {"angle": 0, "text": "医生签名(盖章):", "direction": 1, "handwritten": 0, "position": [707, 2960, 1598, 2972, 1596, 3115, 705, 3106], "score": 0.999, "type": "text"}, {"angle": 0, "text": "符勇", "direction": 1, "handwritten": 1, "position": [2074, 2939, 2598, 2940, 2596, 3341, 2072, 3342], "score": 0.718, "type": "text"}, {"angle": 0, "text": "如需转就近医院注射,请至门诊办必室,", "direction": 1, "handwritten": 0, "position": [579, 3283, 2610, 3341, 2603, 3500, 574, 3440], "score": 0.992, "type": "text"}, {"angle": 0, "text": "盖章有效", "direction": 1, "handwritten": 0, "position": [459, 3463, 943, 3464, 943, 3600, 459, 3602], "score": 0.999, "type": "text"}], "table_cols": 0}], "width": 3024}, "version": "v2.0.0"}}';
// $str = '{"status": true, "msg": "success", "data": [{"plain_text": "树兰(杭州)医院 药品输注证明  患者姓名 梁启芳 联系电话 18958060023 患者身份证号 330624193808120018 用法用量 恩沃利单抗150mg皮下注射QW 注射日期 2024年 7月16日 包装是否完好 是 □否 注射单位盖章", "tables": [{"type": "table_with_line", "table_rows": 8, "table_cols": 4, "header": ["注射药品明细", "药品名称", "药品数量", "规格剂量"], "rows": [["", "恩沃利单抗注射液", "1", "200mg/瓶"], ["用法用量", "恩沃利单抗150mg皮下注射QW", "", ""], ["注射日期", "2024年 7月16日", "", ""], ["包装是否完好", "是 □否", "", ""], ["注射单位盖章", "", "", ""]]}]}]}';
// $res = get_table_text(json_decode($str, true));

// echo $res. PHP_EOL;
