<?php
/**
 * Created by PhpStorm.
 * User: coder
 * Date: 2018/4/9
 * Time: 上午9:37
 */

namespace Coderzhang {
  abstract class DataHelper
  {
    protected $query;
    protected $density;

    abstract protected function createQuery($client, $offset);

    protected function getQuery($offset) {
      if($offset){
        return $this->query. ' offset ' . $offset . 'm';
      }
      return $this->query;
    }

    abstract public function getResult($client);
  }
}

