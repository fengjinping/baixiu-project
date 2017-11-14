<?php
require '../config.php';
//接收数据
$val = $_GET['email'];
//连接数据库
$conn = mysqli_connect(BAIXIU_DB_HOST,BAIXIU_DB_USER,BAIXIU_DB_PASS,BAIXIU_DB_NAME);
if (!$conn) {
	exit('连接失败');
}

mysqli_set_charset($conn, 'utf8');
//查询
$querry = mysqli_query($conn , "select * from users where email = '$val'");
if (!$querry) {
	exit('查询失败');
}
$data = mysqli_fetch_assoc($querry);
//编码为json格式
$json = json_encode($data);
echo $json;