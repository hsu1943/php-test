<?php

$get = $_GET;

// 获取json数据
$json_data = file_get_contents("php://input");
$json = json_decode($json_data, true) ?: [];
$str = json_encode(['get' => $get, 'json' => $json, 'post' => $_POST, 'file' => $_FILES], 320);
file_put_contents('callback.log', date("Y-m-d H:i:s") . ' | ' . $str . PHP_EOL, FILE_APPEND);
echo json_encode(['status' => 'success', 'code' => 0, 'message' => '回调成功'], 320);
exit();
