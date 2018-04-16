<?php
/**
 * Created by PhpStorm.
 * User: coder
 * Date: 2018/4/9
 * Time: 下午10:13
 */

namespace Coderzhang\DataGetter;
require_once 'DataHelper.php';

use \Coderzhang\DataHelper;

class MemoryDataGetter extends DataHelper
{
  public function getQuery($offset) {
    return parent::getQuery($offset);
  }

  public function getResult($client) {
    date_default_timezone_set('Asia/Shanghai');
    $result = ['column' => ['node', '内存使用量', '内存可用量']];

    $resource = array();
    foreach ($this->query as $query) {
      $res = $this->createQuery($client, $query);
      $resource[$query] = json_decode($res->getBody())->data->result;
    }

    $temp = array();
    $node_list = array();

    foreach ($resource as $r_name => $item) {
      foreach ($item as $line) {
        $temp[$r_name][$line->metric->instance] = $line->value[1];
        $node_list[] = $line->metric->instance;
      }
    }

    $node_list = array_unique($node_list);

    foreach ($node_list as $node) {
      $result['rows'][] = [
        'node'  => $node,
        '内存使用量' => floor(($temp['node_memory_MemTotal'][$node] - $temp['node_memory_MemAvailable'][$node])/1000000),
        '内存可用量' => floor($temp['node_memory_MemAvailable'][$node]/1000000)
      ];
    }
    $result['memorySetting'] = new \stdClass();
    $result['memorySetting']->dimension = ['node'];
    $result['memorySetting']->metrics = ['内存使用量','内存可用量'];
    $result['memorySetting']->xAxisType = ['normal'];
    $result['memorySetting']->xAxisName = ['内存'];
    $result['memorySetting']->stack = ['内存'=>['内存使用量','内存可用量']];

    return json_encode($result);
  }

  protected function createQuery($client, $query) {
    return $client->request('GET', $_ENV['API'], [
      'query' => 'query=' . $query
    ]);
  }

  function __construct() {
    $this->query = array('node_memory_MemAvailable', 'node_memory_MemTotal');
  }

}

