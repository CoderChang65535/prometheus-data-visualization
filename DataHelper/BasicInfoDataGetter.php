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
      return json_encode($cache);
    }
    date_default_timezone_set('Asia/Shanghai');

    $resource = array();
    foreach ($this->query as $query) {
      $res = $this->createQuery($client, $query);
      $resource[$query] = json_decode($res->getBody())->data->result;
    }


    $result = array();
    $result['pod'] = [];
    $db = new DatabaseHelper();
    $queryID = md5(time() + rand() + time() + json_encode($this->query));

    foreach ($resource['kubelet_running_pod_count'] as $item) {
      if ($item->metric->instance == $_ENV['MASTER_ALIAS']) {
        $item->metric->instance = $_ENV['MASTER'];
      }
      $db->dbClient()->insert('BasicInfo',
        [
          'queryID' => $queryID,
          'node'    => $item->metric->instance,
          'value'   => $item->value[1],
          'time'    => date('Y-m-d H:i:s', time()),
          'type'    => 'kubelet_running_pod_count'
        ]
      );
      $result['pod'][] = [$item->metric->instance, $item->value[1]];
    }

    foreach ($resource['node_filesystem_size{device="rootfs"}'] as $item) {
      if ($item->metric->instance == $_ENV['MASTER_ALIAS']) {
        $item->metric->instance = $_ENV['MASTER'];
      }
      $db->dbClient()->insert('BasicInfo',
        [
          'queryID' => $queryID,
          'node'    => $item->metric->instance,
          'value'   => $item->value[1],
          'time'    => date('Y-m-d H:i:s', time()),
          'type'    => 'node_filesystem_size'
        ]
      );
      $result['disk'][$item->metric->instance][] = $item->value[1];
    }

    foreach ($resource['node_filesystem_free{device="rootfs"}'] as $item) {
      if ($item->metric->instance == $_ENV['MASTER_ALIAS']) {
        $item->metric->instance = $_ENV['MASTER'];
      }
      $db->dbClient()->insert('BasicInfo',
        [
          'queryID' => $queryID,
          'node'    => $item->metric->instance,
          'value'   => $item->value[1],
          'time'    => date('Y-m-d H:i:s', time()),
          'type'    => 'node_filesystem_free'
        ]
      );
      $result['disk'][$item->metric->instance][] = $item->value[1];
    }


    return json_encode($result);
  }

  protected function createQuery($client, $query) {
    return $client->request('GET', $_ENV['API'], [
      'query' => 'query=' . $query
    ]);
  }

  function __construct($density) {
    $this->query = array(
      '磁盘容量'   => 'node_filesystem_size{device="rootfs"}',
      '磁盘剩余容量' => 'node_filesystem_free{device="rootfs"}',
      'POD信息'  => 'kubelet_running_pod_count',
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

    if (calculateIntervalSecondsFromNow($time) > 10) {
      return null;
    }
    $result = [];

    $queryResult = $db->dbClient()->select('BasicInfo',
      [
        'node',
        'value',
        'time',
        'type'
      ],
      [
        'queryID' => $queryID,
        'type'    => 'kubelet_running_pod_count'
      ]
    );
    foreach ($queryResult as $item) {
      if ($item['type'] == 'kubelet_running_pod_count') {
        $result['pod'][] = [$item['node'], $item['value']];
      }
    }

    $queryResult = $db->dbClient()->select('BasicInfo',
      [
        'node',
        'value',
        'time',
        'type'
      ],
      [
        'queryID' => $queryID,
        'type'    => 'node_filesystem_size'
      ]
    );
    foreach ($queryResult as $item) {
      if ($item['type'] == 'node_filesystem_size') {
        $result['disk'][$item['node']][] = $item['value'];
      }
    }
    $queryResult = $db->dbClient()->select('BasicInfo',
      [
        'node',
        'value',
        'time',
        'type'
      ],
      [
        'queryID' => $queryID,
        'type'    => 'node_filesystem_free'
      ]
    );
    foreach ($queryResult as $item) {
      if ($item['type'] == 'node_filesystem_free') {
        $result['disk'][$item['node']][] = $item['value'];
      }
    }

    return $result;
  }

}

