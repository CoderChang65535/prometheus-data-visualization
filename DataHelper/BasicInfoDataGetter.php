<?php
/**
 * Created by PhpStorm.
 * User: coder
 * Date: 2018/5/12
 * Time: 下午6:55
 */

namespace Coderzhang\DataGetter;
require_once 'DataHelper.php';
require_once 'DatabaseHelper.php';
require_once 'commonTools.php';


use \Coderzhang\DataHelper;
use \Coderzhang\DatabaseHelper;

class BasicInfoDataGetter extends DataHelper
{
  public function getQuery($offset) {
    return parent::getQuery($offset);
  }

  public function getResult($client) {
    $cache = $this->getCache();
    if (!empty($cache)) {
      return $cache;
    }
    date_default_timezone_set('Asia/Shanghai');

    $resource = array();
    foreach ($this->query as $query) {
      $res = $this->createQuery($client, $query);
      $resource[$query] = json_decode($res->getBody())->data->result;
    }


    $result = array();
    $result['pod'] = [];

    foreach ($resource['node_filesystem_size_bytes{device="rootfs"}'] as $item) {
      if ($item->metric->instance == $_ENV['MASTER_ALIAS']) {
        $item->metric->instance = $_ENV['MASTER'];
      }
      $result['disk'][$item->metric->instance][] = $item->value[1];
    }

    foreach ($resource['node_filesystem_free_bytes{device="rootfs"}'] as $item) {
      if ($item->metric->instance == $_ENV['MASTER_ALIAS']) {
        $item->metric->instance = $_ENV['MASTER'];
      }
      $result['disk'][$item->metric->instance][] = $item->value[1];
    }

    foreach ($resource['kube_pod_info'] as $item) {
      if ($item->metric->node == $_ENV['MASTER_ALIAS']) {
        $item->metric->node = $_ENV['MASTER'];
      }
      $result['pod_info'][$item->metric->node][] =
        str_replace($_ENV['MASTER_ALIAS'], $_ENV['MASTER'], $item->metric->pod);
    }

    $temp = [];
    foreach ($result['pod_info'] as $key => $item) {
      $temp[$key] = array_unique($item);
    }
    $result['pod_info'] = $temp;

    foreach ($resource['kubelet_running_pod_count'] as $item) {
      if ($item->metric->instance == $_ENV['MASTER_ALIAS']) {
        $item->metric->instance = $_ENV['MASTER'];
      }
      $result['pod'][] = [$item->metric->instance, count($result['pod_info'][$item->metric->instance])];
    }

    $db = new DatabaseHelper();
    $queryID = md5(json_encode($this->query) . time() . rand() . time());
    $db->dbClient()->insert('BasicInfo',
      [
        'queryId' => $queryID,
        'time'    => date('Y-m-d H:i:s', time()),
        'value'   => json_encode($result),
        'node'    => ''
      ]
    );

    return json_encode($result);
  }

  protected function createQuery($client, $query) {
    return $client->request('GET', $_ENV['API'], [
      'query' => 'query=' . $query
    ]);
  }

  function __construct($density) {
    $this->query = array(
      '磁盘容量'   => 'node_filesystem_size_bytes{device="rootfs"}',
      '磁盘剩余容量' => 'node_filesystem_free_bytes{device="rootfs"}',
      'POD数量'  => 'kubelet_running_pod_count',
      'POD信息'  => 'kube_pod_info',
    );
  }

  protected function getCache() {
    $db = new DatabaseHelper();

    $timeResult = $db->dbClient()->select('BasicInfo',
      [
        'time',
        'queryID'
      ],
      [
        'LIMIT' => 1,
        'ORDER' => [
          'time' => 'DESC'
        ]
      ]
    );
    foreach ($timeResult as $item) {
      $time = $item['time'];
      $queryID = $item['queryID'];
    }
    if (farAwayOverOneMinute($time)) {
      return null;
    }

    $queryResult = $db->dbClient()->select('BasicInfo',
      [
        'node',
        'value',
        'time'
      ],
      [
        'queryID' => $queryID
      ]
    );

    foreach ($queryResult as $item) {
      return $item['value'];
    }
  }

}

