<?php

define('DBHOST', 'localhost');
define('DBUSER','root');
define('DBPWD','111');
define('DBNAME','chat');

/**
 * Memcached 服务器地址
 * @var String
 */
define('MEMHOST','127.0.0.1');
/**
 * 每条信息保存在Memcached中的时间 
 * 默认：30秒
 * @var int
 *
 */
define('MSGSAVETIME',30);
