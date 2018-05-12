<?php
/**
 * Created by PhpStorm.
 * User: coder
 * Date: 2018/4/7
 * Time: 下午4:53
 */


require_once 'bootstrap.php';

$client = new GuzzleHttp\Client();

if (isset($_GET['target'])) {


  switch ($_GET['target']) {
    case 'cpu':
      $CPU = new \Coderzhang\DataGetter\CPUDataGetter(20);
      print $CPU->getResult($client);
      break;
    case 'memory':
      $memory = new \Coderzhang\DataGetter\MemoryDataGetter();
      print $memory->getResult($client);
      break;
    case 'tcp':
      $tcp = new \Coderzhang\DataGetter\TCPDataGetter();
      print $tcp->getResult($client);
      break;
    case 'basic':
      $basic = new \Coderzhang\DataGetter\BasicInfoDataGetter();
      print $basic->getResult($client);
      break;
  }

}

//$res = $client->request('GET', $_ENV['API'], [
//  'query' => 'query=rate(http_requests_total[10m])'
//]);


