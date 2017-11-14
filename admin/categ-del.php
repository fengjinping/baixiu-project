<?php
//获取传递过来的id值
if (empty($_GET['id'])) {
  // 缺失必要的ID参数
  die('缺失必要的ID参数');
}

$id = $_GET['id'];

//连接数据库查询
require_once '../func.php';
$affected_rows = msq_change('delete from categories where id in (' . $id . ');');

var_dump($affected_rows);

//跳转
header('Location:/admin/categories.php');