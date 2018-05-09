<?php
/**
 * Created by PhpStorm.
 * User: coder
 * Date: 2018/5/7
 * Time: 下午5:00
 */

function convertTimeWithoutSeconds($time) {
  $timestamp = strtotime($time);
  $result = date('Y-m-d H:i:00', $timestamp);
  return $result;
}

function convertTimestampWithoutSeconds($timestamp) {
  $result = date('Y-m-d H:i:00', $timestamp);
  return $result;
}

function convertTimeToTimestampWithoutSeconds($time) {
  $timestamp = strtotime($time);
  $time = date('Y-m-d H:i:00', $timestamp);
  $timestamp = strtotime($time);
  return $timestamp;
}

function convertTimeToDay($time) {
  $timestamp = strtotime($time);
  return date('H:i:s', $timestamp);;
}

function calculateIntervalSeconds($time1, $time2) {

}