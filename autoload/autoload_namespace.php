<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/19
 * Time: 上午10:14
 */
$autoloadFile = dirname(__FILE__);
$baseDir = dirname($autoloadFile);

$nameSpace = array(
    'MiddlewareSpace' => array($baseDir),
    'CommonSpace' => array($baseDir),
    'UtilSpace' => array($baseDir),
);
return $nameSpace;
