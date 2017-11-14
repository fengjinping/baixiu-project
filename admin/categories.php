<?php
//引入检测session文件
require_once '../func.php';
if_user();

//添加操作
function add_info() {
  if (empty($_POST['name']) || empty($_POST['slug'])) {
    $GLOBALS['error_msg'] = '请完整填写表单';
    return;
  }

  $name = $_POST['name'];
  $slug = $_POST['slug'];

  //连接数据库添加数据
  $affected_rows = msq_change("insert into categories values(null, '{$slug}', '{$name}')");

  //判断影响行数
  if ($affected_rows === 1) {
    $GLOBALS['success_msg'] = '添加成功';
  }

}

//编辑操作
function edit_info() {
  if (empty($_POST['id']) || empty($_POST['name']) || empty($_POST['slug'])) {
    $GLOBALS['error_msg'] = '请完整填写表单';
    return;
  }

  $name = $_POST['name'];
  $slug = $_POST['slug'];
  $id = $_POST['id'];

  //连接数据库添加数据
  $affected_rows = msq_change("update categories set name = '$name' , slug = '$slug' where id = $id");

  //判断影响行数
  if ($affected_rows === 1) {
    $GLOBALS['success_msg'] = '修改成功';
  }

}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (empty($_POST['id'])) {
    add_info();
  } else {
    edit_info();
  }
  
}

//连接从数据库中取数据
$categories = msq_fetch_all('select * from categories');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
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
          <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method='post'>
            <h2>添加新分类目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary btn-save" type="submit">添加</button>
              <button class="btn btn-default btn-cancel" type="submit" style="display: none">取消</button>
            </div>
            <input type="hidden" name="id" id="id" value="0">
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" href="javascript:;" id="btn_del" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $value): ?>
                <tr>
                  <td class="text-center"><input data-id="<?php echo $value['id'] ?>" type="checkbox"></td>
                  <td><?php echo $value['name'] ?></td>
                  <td><?php echo $value['slug'] ?></td>
                  <td class="text-center">
                    <button class="btn btn-info btn-edit btn-xs" data-name="<?php echo $value['name'] ?>" data-slug="<?php echo $value['slug'] ?>" data-id="<?php echo $value['id'] ?>">编辑</button>
                    <a href="/admin/categ-del.php?id=<?php echo $value['id'] ?>" class="btn btn-danger btn-xs">删除</a>
                  </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'categories'; ?>
  <?php include './inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function($){
      //声明一个数组  存放选中行对应id
      var checkeds = []

      $('tbody input').on('change', function(){
          //任意复选框状态发生改变则执行
          var $this = $(this)
          var id = $this.attr('data-id')

          if ($this.prop('checked')) {
            checkeds.push(id)
          } else {
            checkeds.splice(checkeds.indexOf(id), 1)
          }

          //根据是否有选中判断隐藏或删除
          checkeds.length ? $('#btn_del').fadeIn() : $('#btn_del').fadeOut()

          //改变批量删除的问号参数
          $('#btn_del').prop('href', '/admin/categ-del.php?id=' + checkeds)
      })
      
    })

    //  编辑功能
    $(function($){
      $('tbody').on('click', '.btn-edit', function(){
          //获取当前行信息展示到左侧
          var name = $(this).data('name')
          var slug = $(this).data('slug')
          var id = $(this).data('id')

          $('form h2').text('编辑分类')
          $('form .btn-save').text('保存')
          $('form .btn-cancel').fadeIn()
          $('#name').val(name)
          $('#id').val(id)
          $('#slug').val(slug)
      })

      // 取消编辑
      $('.btn-cancel').on('click', function () {
        $('form h2').text('添加新分类目录')
        $('form .btn-save').text('添加')
        $('form .btn-cancel').fadeOut()
        $('#name').val('')
        $('#slug').val('')
        return false // 组织当前按钮导致的表单提交
      })
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
