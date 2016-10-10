<?php
return array(
	//'配置项'=>'配置值'
	    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  'localhost', // 服务器地址
    'DB_NAME'               =>  'renren',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  'root',          // 密码
    //显示页面Trace信息
    'SHOW_PAGE_TRACE'		=>   true,
    //设置允许模块
    'MODULE_ALLOW_LIST'    =>    array('Home','Admin'),
    //设置默认模块设置
    'DEFAULT_MODULE'       =>    'Home',
);