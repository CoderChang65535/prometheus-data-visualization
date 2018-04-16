<?php
/**
 * Created by PhpStorm.
 * User: coder
 * Date: 2018/4/10
 * Time: 下午2:56
 */

namespace Coderzhang\DataGetter;
require_once 'DataHelper.php';

use \Coderzhang\DataHelper;

class TCPDataGetter extends DataHelper
{
  public function getQuery($offset) {
    return parent::getQuery($offset);
  }

  public function getResult($client) {
    date_default_timezone_set('Asia/Shanghai');
    $result = [];

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
      $var = array();
      $body = array();
      foreach ($this->query as $key => $query) {
        $body[] = ['type' => $key, $node => $temp[$query][$node]];
      }
      $var['rows'] = $body;
      $var['columns'] = ['type', $node];
      $obj = new \stdClass();
      $obj->dimension = 'type';
      $obj->metrics = $node;
      $obj->selectedMode = 'single';
      $obj->hoverAnimation = 'false';
      $var['tcpSettings'] = $obj;
      $result[] = $var;
    }

    return json_encode($result);
  }

  protected function createQuery($client, $query) {
    return $client->request('GET', $_ENV['API'], [
      'query' => 'query=' . $query
    ]);
  }

  function __construct() {
    $this->query = array(
      '链接信息错误' => 'node_netstat_Tcp_InErrs',
      '监听断开'   => 'node_netstat_TcpExt_ListenDrops',
      '连接超时'   => 'node_netstat_TcpExt_TCPTimeouts',
      '重连丢失'   => 'node_netstat_TcpExt_TCPLostRetransmit',
      '异常关闭'   => 'node_netstat_Tcp_EstabResets'
    );
  }

}

