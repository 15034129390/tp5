<?php
include_once("connect.php");

$query=mysql_query("select * from sortlist where id=1");
if($rs=mysql_fetch_array($query)){
   	$sort=$rs['sort'];
}
$sort_arr=explode(",",$sort);
$len=count($sort_arr);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="marquee, jquery插件" />
<meta name="description" content="Helloweba演示平台，演示XHTML、CSS、jquery、PHP案例和示例" />
<title>拖动布局并将排序结果保存到数据库-Helloweba演示平台</title>
<link rel="stylesheet" type="text/css" href="../css/main.css" />
<style type="text/css">
#module_list{margin-left:4px}
.modules{float:left; width:200px; height:140px; margin:10px; overflow:hidden; border:1px solid #acc6e9; background:#e8f5fe}
.m_title{height:24px; line-height:24px; background:#afc6e9}
#loader{height:24px; text-align:center}
.clear{clear:both}
</style>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js'></script>
<script type="text/javascript" src="idrag.js"></script>
</head>

<body>
<div id="header">
   <div id="logo"><h1><a href="http://www.helloweba.com" title="返回helloweba首页">helloweba</a></h1></div>
</div>

<div id="main">
  <h2 class="top_title"><a href="http://www.helloweba.com/view-blog-72.html">jQuery实现拖动布局并将排序结果保存到数据库</a></h2>
  <div id="loader"></div>
  <div id="module_list">
   <input type="hidden" id="orderlist" value="<?php echo $sort;?>" />
   <?php 
     for($i=0;$i<$len;$i++){
   ?>
   <div class="modules" title="<?php echo $sort_arr[$i]; ?>">
      <h3 class="m_title">Module:<?php echo $sort_arr[$i]; ?></h3>
      <p><?php echo $sort_arr[$i]; ?></p>
   </div>
   <?php } ?>
   <div class="clear"></div>
  </div>
</div>
<div id="footer">
    <p>Powered by helloweba.com  允许转载、修改和使用本站的DEMO，但请注明出处：<a href="http://www.helloweba.com">www.helloweba.com</a></p>
</div>
<p id="stat"><script type="text/javascript" src="http://js.tongji.linezing.com/1870888/tongji.js"></script></p>
</body>
</html>