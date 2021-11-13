<?php
// Videna Framework
// File: /Videna/Core/Log.php
// Desc: Add log to file

namespace Videna\Core;

abstract class Log {


  // Put errors and debug info in log file
  public static function add($data = ['Break Point'], $die = FALSE) {

    $fp = fopen(PATH_APP_LOG, "a");

    $logLine = date("Y-m-d H:i:s").($die ? ' FATAL ERROR' : ' DEBUG/INFO') . "\r\n";    
    foreach ( $data as $error_descr ) $logLine .= $error_descr."\r\n";    
    $logLine .= "\r\n";

    $result = fwrite($fp, $logLine);
    fclose($fp);

    if ($die) {

      if ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) {
        die( json_encode([ 
          'response' => 500, 
          'status' => $die 
        ]) );
      }
     
      http_response_code(500);
      die( $die );
      
    }
  }


  public static function read($file = PATH_APP_LOG) {

    if ( !is_file($file) ) return false;

    $lines = file($file);

    foreach ($lines as $line_num => $line) $log[$line_num] = $line;

    return $log;

  }


  public static function delete($file = PATH_APP_LOG) {
    
    if ( is_file($file) ) {
      if (unlink($file)) {
        return 200;
      }
      else return 500;
    }

    return 404;

  }

} // END Class Log