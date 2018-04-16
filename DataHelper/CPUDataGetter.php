<?php
/**
 * Created by PhpStorm.
 * User: coder
 * Date: 2018/4/9
 * Time: 上午10:27
 */

namespace Coderzhang\DataGetter;
require_once 'DataHelper.php';

use \Coderzhang\DataHelper;

class CPUDataGetter extends DataHelper
{
  public function getQuery($offset) {
    return parent::getQuery($offset);
  }

  public function getResult($client) {
    date_default_timezone_set('Asia/Shanghai');
    $key = ['column' => ['time']];

    $result = array();
    for ($i = $this->density - 1; $i >= 0; $i--) {
      $offset = $i * 5;
      $res = $this->createQuery($client, $offset);
      $resource = json_decode($res->getBody())->data->result;

      $line = array();
      foreach ($resource as $item) {
        if ($item->metric->instance == $_ENV['MASTER_ALIAS']) {
          $item->metric->instance = $_ENV['MASTER'];
        }
        $key['column'][] = $item->metric->instance;
        $line[$item->metric->instance] = round($item->value[1] * 100, 2);
        $line['time'] = date('H:i:s', $item->value[0] - 5 * 60 * $i);
      }
      $result[] = $line;
    }
    $key['column'] = array_unique($key['column']);
    $key['result'] = $result;
    return json_encode($key);
  }

  protected function createQuery($client, $offset) {
    return $client->request('GET', $_ENV['API'], [
      'query' => $this->getQuery($offset) . ')'
    ]);
  }

  function __construct($density) {
    $this->query = 'query=rate(process_cpu_seconds_total{job="kubernetes-nodes"}[10m]';
    $this->density = $density;
  }

}

