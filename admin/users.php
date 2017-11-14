<?php
//引入检测session文件
require_once '../func.php';
if_user();

//表单添加
function add_info() {
  //表单校验
  if (empty($_POST['email']) || empty($_POST['slug']) || empty($_POST['nickname']) || empty($_POST['password'])){
    $GLOBALS['error_msg'] = '请完整填写表单';
    return;
  }

  if (!(isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK)) {
    $GLOBALS['error_msg'] = '请完整填写表单';
    return;
  }

  $path = '../static/uploads'. $_FILES['avatar']['name'];
  //移动图片路径
  move_uploaded_file($_FILES['avatar']['tmp_name'], $path);

  //获取变量
  $email = $_POST['email'];
  $slug = $_POST['slug'];
  $nickname = $_POST['nickname'];
  $password = $_POST['password'];
  $avatar = substr($path, 2);

  //数据持久化
  $affected_rows = msq_change("insert into users values( null , '{$slug}' , '{$email}' , '{$password}' , '{$nickname}' , '{$avatar}' , null , 'activated')");

  if ($affected_rows === 1) {
    $GLOBALS['success_msg'] = '添加成功';
  }
}

//表單修改
function edit_indo(){
//表单校验
  if (empty($_POST['email']) || empty($_POST['slug']) || empty($_POST['nickname']) || empty($_POST['password'])){
    $GLOBALS['error_msg'] = '请完整填写表单';
    return;
  }

  if (!(isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK)) {
    $GLOBALS['error_msg'] = '请完整填写表单';
    return;
  }

  $path = '../static/uploads'. $_FILES['avatar']['name'];
  //移动图片路径
  move_uploaded_file($_FILES['avatar']['tmp_name'], $path);

  //获取变量
  $email = $_POST['email'];
  $slug = $_POST['slug'];
  $nickname = $_POST['nickname'];
  $password = $_POST['password'];
  $avatar = substr($path, 2);
  $id = $_POST['id'];

  //数据持久化
  $affected_rows = msq_change("updata users set id = null , slug = '{$slug}' , email = '{$email}' , password ='{$password}' , nickname = '{$nickname}' , avatar = '{$avatar}' , bio = null , status = 'activated')");

  if ($affected_rows === 1) {
    $GLOBALS['success_msg'] = '添加成功';
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (empty($_POST['id'])) {
    add_info();
  } else {
    edit_info();
  }
}
//查询数据进行页面数据迭代
$data = msq_fetch_all('select * from users');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include './inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger">
          <strong>错误！</strong><?php echo $error_msg ?>
        </div>
      <?php endif ?>

      <!-- 添加成功时显示 -->
      <?php if (isset($success_msg)): ?>
        <div class="alert alert-success">
          <strong>成功！</strong><?php echo $success_msg ?>
        </div>
      <?php endif ?>

      <div class="row">
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
            <h2>添加新用户</h2>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱">
            </div>
            <div class="form-group">
              <label for="image">图片</label>
              <!-- show when image chose -->
              <img class="help-block thumbnail" style="display: none">
              <input id="image" class="form-control" name="avatar" type="file">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码">
            </div>
            <input type="hidden" name="id" id="id" value="0">
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
              <button class="btn btn-default btn-cancel" type="submit" style="display: none">取消</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" id="btn_del" href="javascript:;" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($data as $value): ?>
                <tr>
                  <td class="text-center"><input type="checkbox" data-id="<?php echo $value['id'] ?>"></td>
                  <td class="text-center"><img class="avatar" src="<?php echo $value['avatar'] ?>"></td>
                  <td><?php echo $value['email'] ?></td>
                  <td><?php echo $value['slug'] ?></td>
                  <td><?php echo $value['nickname'] ?></td>
                  <td><?php echo $value['status'] ?></td>
                  <td class="text-center">
                    <button class="btn btn-default btn-edit btn-xs" data-email="<?php echo $value['email'] ?>" data-id="<?php echo $value['id'] ?>" data-slug="<?php echo $value['slug'] ?>" data-nickname="<?php echo $value['nickname'] ?>" data-password="<?php echo $value['password'] ?>" data-avatar="<?php echo $value['avatar'] ?>">编辑</button>
                    <a href="/admin/user-del.php?id=<?php echo $value['id'] ?>" class="btn btn-danger btn-xs">删除</a>
                  </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'users'; ?>
  <?php include './inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function($){
        //执行批量删除工作
      var arr = []
      $('tbody').on('change','input', function(){
        var $this = $(this)
        var $id = $this.attr('data-id')
  
        if ($this.prop('checked')) {
          arr.push($id)
        } else {
          arr.splice(arr.indexOf($id), 1)
        }
  
        arr.length ? $('#btn_del').fadeIn() : $('#btn_del').fadeOut()
  
        $('#btn_del').prop('href','/admin/user-del.php?id=' + arr)
      })
    })

    $(function($) {
      $('tbody').on('click', '.btn-edit', function(){
        //获取当前行信息
        var email = $(this).data('email')
        var slug = $(this).data('slug')
        var nickname = $(this).data('nickname')
        var password = $(this).data('password')
        var id = $(this).data('id')
        var avatar = $(this).data('avatar')
        
        //进行添加
        $('form h2').text('编辑用戶')
        $('form .btn-cancel').fadeIn()
        $('#id').val(id)
        $('#email').val(email)
        $('#slug').val(slug)
        $('#nickname').val(nickname)
        $('#password').val(password)
        $('#img').prop('src', avatar)
      })

      //取消編輯
      $('.btn-cancel').on('click', function(){
        $('form h2').text('添加新用戶')
        $('form .btn-cancel').fadeOut()
        $('#id').val('')
        $('#email').val('')
        $('#slug').val('')
        $('#nickname').val('')
        $('#password').val('')
        return false
      })


    })

  </script>
  <script>NProgress.done()</script>
</body>
</html>
