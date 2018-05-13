<?php
/**
 * Created by PhpStorm.
 * User: coder
 * Date: 2018/4/9
 * Time: 上午9:37
 */


namespace Coderzhang {
  /**
   * Class DataHelper
   * @package Coderzhang
   */
  abstract class DataHelper
  {
    /**
     * @var query statement
     */
    protected $query;
    /**
     * @var query density
     */
    protected $density;

    /**
     * @param $client http client
     * @param $offset offset from density
     * @return mixed return formatted query statement
     */
    abstract protected function createQuery($client, $offset);

    /**
     * @param $offset offset
     * @return query|string format offset to query
     */
    protected function getQuery($offset) {
      if ($offset) {
        return $this->query . ' offset ' . $offset . 'm';
      }
      return $this->query;
    }

    /**
     * @param $client http client
     * @return mixed result
     */
    abstract public function getResult($client);

    /**
     * @return mixed
     */
    abstract protected function getCache();
  }
}
