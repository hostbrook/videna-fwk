<?php
// Videna Framework
// File: /Videna/Core/DataArray.php
// Desc: Trait to work with array data

namespace Videna\Core;


trait DataArray {
  
  private static $data = [];
  
  public static function get($field) {
    if ( array_key_exists($field, self::$data) ) return self::$data[$field];
    return null;
  }

  public static function set($fields) {
    foreach ($fields as $key => $value) {
      self::$data[$key] = $value;
    }
  }
  
  public static function getAll() {
    return self::$data;
  }
  
  public static function setAll($dataArray) {
    self::$data = $dataArray;
  }

}