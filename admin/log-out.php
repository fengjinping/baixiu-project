<?php
//找到当前用户登录的箱子
session_start();

//删除记录用户登录状态的数据
unset($_SESSION['logged']);

//跳转到登录页
header('Location: /admin/login.php');