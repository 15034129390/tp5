<?php

// [ 应用入口文件 ]
// 定义应用目录
define('APP_PATH', __DIR__ . '/../app/');
// 绑定当前访问到admin模块
define('BIND_MODULE','wap');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';