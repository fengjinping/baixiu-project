<?php
//引入配置文件
require_once 'config.php';

/**
 * 公共函数封装
 */

//检测session
session_start();
function if_user(){
	if (empty($_SESSION['logged'])) {
  		header('Location:/admin/login.php');
  		return;
	}
	return $_SESSION['logged'];
}


/**
 *建立与数据库的连接并返回连接对象
  每次使用后需要自己关闭连接
 */

function msq_connect () {
	$con = mysqli_connect(BAIXIU_DB_HOST, BAIXIU_DB_USER, BAIXIU_DB_PASS, BAIXIU_DB_NAME);

	mysqli_set_charset($con, 'utf8');

	if (!$con) {
	  exit('连接错误');
	}
	return $con;
}


/**
 *执行一个sql语句，得到执行结果
 *是个列表
*/
function msq_fetch_all($sql) {
	$con = msq_connect();

	$query = mysqli_query($con, $sql);

	if (!$query) {
		//查询失败了
		return false;
	}

	while ($row = mysqli_fetch_assoc($query)) {
		$data[] = $row;
	}

	//释放结果集
	mysqli_free_result($query);

	//断开与服务端的连接
	mysqli_close($con);

	return $data;
}


/**
 *执行一个sql语句，得到执行结果
 *只取第一个
*/
function msq_fetch_one($sql) {
	return msq_fetch_all($sql)[0];
}


function msq_change($sql) {
	$con = msq_connect();

	$query = mysqli_query($con, $sql);

	if (!$query) {
		//查询失败了
		return false;
	}

	//判断影响行数
	$affected_rows = mysqli_affected_rows($con);

	//断开连接
	mysqli_close($con);

	return $affected_rows;
}