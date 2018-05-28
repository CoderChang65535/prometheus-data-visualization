<?php
/**
 * Created by PhpStorm.
 * User: coder
 * Date: 2018/4/9
 * Time: 上午10:27
 */

namespace Coderzhang\DataGetter;
require_once 'DataHelper.php';
require_once 'DatabaseHelper.php';
require_once 'commonTools.php';


use \Coderzhang\DataHelper;
use \Coderzhang\DatabaseHelper;

class CPUDataGetter extends DataHelper
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
    $key = ['column' => ['time']];

    $result = array();
    $db = new DatabaseHelper();
    $queryID = md5(json_encode($this->query).time().rand().time());
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

        // save cache to db
        $queryResult = $db->dbClient()->select('CPU', ['value'],
          [
            'time' => date('Y-m-d H:i:s', $item->value[0] - 5 * 60 * $i),
            'node' => $item->metric->instance
          ]
        );
        if (empty($queryResult)) {
          $db->dbClient()->insert('CPU',
            [
              'queryId' => $queryID,
              'time'    => date('Y-m-d H:i:s', $item->value[0] - 5 * 60 * $i),
              'value'   => $line[$item->metric->instance],
              'node'    => $item->metric->instance
            ]
          );
        }
      }
      if (empty($line)) {
        continue;
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

  protected function getCache() {
    $db = new DatabaseHelper();

    $timeResult = $db->dbClient()->select('CPU',
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

    $queryResult = $db->dbClient()->select('CPU',
      [
        'node',
        'value',
        'time'
      ],
      [
        'queryID' => $queryID
      ]
    );
    $resultSet = [];
    $result = ['column' => ['time'], 'result' => []];
    foreach ($queryResult as $item) {
      $resultSet[$item['time']][] = [$item['node'] => $item['value']];
      $result['column'][] = $item['node'];
    }
    if (!empty($resultSet)) {
      foreach ($resultSet as $item_time => $item) {
        $line = ['time' => convertTimeToHIS($item_time)];
        foreach ($item as $key => $iitem) {
          foreach ($iitem as $kkey => $iiitem) {
            $line[$kkey] = $iiitem;
          }
        }
        $result['result'][] = $line;
      }
    }
//    if (!empty($timeResult) && sizeof($timeResult) == 10) {
//      $resultSet = [];
//      foreach ($timeResult as $item) {
//        $line['time'] = convertTimeToDay($item);
//
//        foreach ($queryResult as $item2) {
//          $line[$item2['node']] = $item2['value'];
//        }
//        $resultSet[] = $line;
//      }
//    }
    $result['column'] = array_unique($result['column']);
    return $result;
  }

}

