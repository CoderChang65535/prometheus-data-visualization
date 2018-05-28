<?php
/**
 * Created by PhpStorm.
 * User: coder
 * Date: 2018/4/28
 * Time: 上午10:48
 */

namespace Coderzhang;

require_once __DIR__ . "/../vendor/autoload.php";

use Medoo\Medoo;

/**
 * Class DatabaseHelper
 * @package Coderzhang
 */
class DatabaseHelper
{
  private $database;

  public function __construct() {
    $env = new \Dotenv\Dotenv(__DIR__ . '/..');
    $env->load();
    $this->database = new Medoo([
      'database_type' => $_ENV['DB_TYPE'],
      'database_name' => $_ENV['DB_NAME'],
      'server'        => $_ENV['DB_SERVER'],
      'username'      => $_ENV['DB_USER'],
      'password'      => $_ENV['DB_PASS']
    ]);
  }

  public function dbClient() {
    return $this->database;
  }
}
