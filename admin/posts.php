<?php
//引入检测session文件
require_once '../func.php';
if_user();

//筛选处理================================================
$where = '1 = 1';

//类别语句
if (isset($_GET['category']) && $_GET['category'] !== 'all') {
  $where .= ' and posts.category_id =' . $_GET['category'];
}

//状态语句
if (isset($_GET['status']) && $_GET['status'] !== 'all') {
  $where .= ' and posts.status =\'' . $_GET['status'] .'\'';
}

//分页处理=================================================
$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];

//每页查询数量
$size = 20;

//获取数据库中共多少数据
$count = (int)msq_fetch_one('select count(1) as count from posts where ' . $where)['count'];

//获取总条数
$num = (int)ceil($count/$size);
//跳过查询量
$off = ($page -1) * $size; 
$began = $page - 2 < 1 ? 1 : $page - 2;
$end = $began + 4;
if ($end > $num) {
  $end = $num;
  $began = $end - 4 < 1 ? 1 : $end - 4;
}


//获取数据=================================================
$posts = msq_fetch_all('select 
  posts.id,
  posts.title,
  posts.created,
  posts.status,
  users.nickname as user_name,
  categories.name as category_name
  from posts 
  inner join users on posts.user_id = users.id
  inner join categories on posts.category_id = categories.id
  where ' . $where . '
  order by posts.created desc
  limit ' . $off . ', ' . $size);
//获取类型数据
$categories = msq_fetch_all('select * from categories');

//转换时间的函数
function convert_date ($date) {
  $timestamp = strtotime($date);
  // 由于 r 在时间格式中有特殊含义，如果需要原封不动的标识 一个 r 转义一下
  return date('Y年m月d日<b\r>H:i:s', $timestamp);
}

//转换状态的函数
function convert_status ($status) {
  $dict = array(
    'drafted' => '草稿',
    'published' => '已发布',
    'trashed' => '回收站'
  );

  return isset($dict[$status]) ? $dict[$status] : '未知';
}


?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach ($categories as $value): ?>
              <option value="<?php echo $value['id'] ?>" <?php echo isset($_GET['category']) && $_GET['category'] == $value['id'] ? 'selected' : ''?>><?php echo $value['name'] ?></option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="drafted" <?php echo isset($_GET['status']) && $_GET['status'] == 'drafted' ? 'selected' : ''?>>草稿</option>
            <option value="published" <?php echo isset($_GET['status']) && $_GET['status'] == 'published' ? 'selected' : ''?>>已发布</option>
            <option value="trashed" <?php echo isset($_GET['status']) && $_GET['status'] == 'trashed' ? 'selected' : ''?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="#">上一页</a></li>
          <?php  for ($i=$began; $i <= $end; $i++) { ?>
            <li><a href="/admin/posts.php?page=<?php echo $i ?>&&category=<?php echo empty($_GET['category']) ? 'all' : $_GET['category'] ?>&&status=<?php echo empty($_GET['status']) ? 'all' : $_GET['status'] ?>"><?php echo $i ?></a></li>
          <?php  } ?>
          <li><a href="#">下一页</a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $value): ?>
            <tr>
              <td class="text-center"><input type="checkbox"></td>
              <td><?php echo $value['title'] ?></td>
              <td><?php echo $value['user_name'] ?></td>
              <td><?php echo $value['category_name'] ?></td>
              <td class="text-center"><?php echo convert_date($value['created']) ?></td>
              <td class="text-center"><?php echo convert_status($value['status']) ?></td>
              <td class="text-center">
                <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
                <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
              </td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'posts'; ?>
  <?php include './inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
