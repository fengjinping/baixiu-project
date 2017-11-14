<?php
function login(){
  //1-数据校验
  if (empty($_POST['email'])) {
    $GLOBALS['error_msg'] = 'what is your name?';
    return;
  }
  if (empty($_POST['password'])) {
    $GLOBALS['error_msg'] = 'what is your password?';
    return;
  }
  $email = $_POST['email'];
  $password = $_POST['password'];
  //连接数据库读取数据
  //引入配置文件
  require '../config.php';
  //连接数据库
  $con = mysqli_connect(BAIXIU_DB_HOST,BAIXIU_DB_USER,BAIXIU_DB_PASS,BAIXIU_DB_NAME);
  if (!$con) {
    $GLOBALS['error_msg'] = '连接数据库失败';
    return;
  }
  //设置字符集
  mysqli_set_charset($con,'utf8');
  //数据库查询
  $query = mysqli_query($con , "select * from users where email = '$email'");
  if (!$query) {
    $GLOBALS['error_msg'] = '查询失败，换个油箱？';
    return;
  }
  //获取字符集
  $result = mysqli_fetch_assoc($query);
  if ($result['email'] !== $email) {
    $GLOBALS['error_msg'] = '用户名或密码错误';
    return;
  }
  if ($result['password'] !== $password) {
    $GLOBALS['error_msg'] = '用户名或密码错误';
    return;
  }
  //设置session
  session_start();
  $_SESSION['logged'] = $result;
  //2-跳转
  header('Location:/admin/');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  login();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
</head>
<body>
  <div class="login">
    <form class="login-wrap" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>" novalidate>
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger">
          <strong>错误！</strong> <?php echo $error_msg ?>
        </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" type="email" name="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" type="password" name="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>

  <script src='/static/assets/vendors/jquery/jquery.js'></script>
  <script>
    $(function(){
      //设置文本框失焦事件
      $('#email').blur(function(){
        //设置图片的默认src属性
        $('img').attr("src","/static/assets/img/default.png")
        //获取文本框内的值
        $value = $(this).val()
        //通过ajax获取email值为$value的数据
        $.get('/admin/back_data.php', { email: $value }, function (res) {
            //获取数据
            $data = JSON.parse(res)
            //判断是否获取到数据，如果为null 则return
            if ($data == null) {return} 
            //获取图片路径
            $avatar = $data.avatar
            $('img').attr("src" , $avatar)
        })
      })
    })
  </script>
</body>
</html>