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


}

//$res = $client->request('GET', $_ENV['API'], [
//  'query' => 'query=rate(http_requests_total[10m])'
//]);

$CPU = new \Coderzhang\DataGetter\CPUDataGetter(20);

print $CPU->getResult($client);
