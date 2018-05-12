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

function convertTimeToHIS($time) {
  date_default_timezone_set('Asia/Shanghai');
  $timestamp = strtotime($time);
  return date('H:i:s', $timestamp);;
}

function calculateIntervalSecondsFromNow($time) {
  date_default_timezone_set('Asia/Shanghai');

  if (empty($time)) {
    return true;
  }
  $timestamp = strtotime($time);
  return abs(time() - $timestamp);

}

function farAwayOverOneMinute($time) {
  return calculateIntervalSecondsFromNow($time) > 60;
}