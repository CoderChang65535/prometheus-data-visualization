<?php
/**
 * Created by PhpStorm.
 * User: coder
 * Date: 2018/4/7
 * Time: 下午1:50
 */

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/DataHelper/CPUDataGetter.php';
require_once __DIR__.'/DataHelper/MemoryDataGetter.php';
require_once __DIR__.'/DataHelper/TCPDataGetter.php';

use \Dotenv\Dotenv;
use \GuzzleHttp\Client;

$env = new Dotenv(__DIR__);
$env->load();

