<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');

// 定义APP新闻列表每页显示文章数量10
define('NEWS_NUMBER',10); 

// 定义APP新闻列表每页显示文章数量10
define('COURSE_NUMBER',10); 

//服务器地址
define("SITE_URL","http://10.10.11.48/"); 

define("PRE_URL","http://10.10.11.48/index.php/"); 

//静态CSS文件地址
define("CSS_URL",SITE_URL."static/css/");

//静态图片地址
define("IMG_URL",SITE_URL."static/img/");

//静态JS文件
define("JS_URL",SITE_URL."static/js/");

//ueditor引用文件
define("UEDITOR_URL",SITE_URL."editor/");

//图片上传地址
define("UPLOADS_IMG_URL",SITE_URL."uploads/image/");

//音频上传地址
define("UPLOADS_AUDIO_URL",SITE_URL."uploads/audio/");

//视频上传地址
define("UPLOADS_VIDEO_URL",SITE_URL."uploads/video/");

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
