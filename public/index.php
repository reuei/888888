<?php
/**
 * 公共入口 - 所有请求都转发到根 index.php
 */
$rootPath = dirname(__DIR__);
require $rootPath . '/index.php';
