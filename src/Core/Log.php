<?php

/**
 * Add debug info or exception of fatal errors info log file
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Core;

class Log
{

    /**
     * Put errors and debug info in log file
     * @param mixed $data An array or string with log info
     * @param mixed $die Bool or string - the output message if stop script is required
     * @return void
     */
    public static function add($data, $die = FALSE, $level = INFO)
    {

        $fp = fopen(PATH_APP_LOG, "a");

        $logLine = '[' . date("Y-m-d H:i:s") . '] ' . ($die ? 'FATAL ERROR' : $level) . "\r\n";

        if (is_array($data)) {
            foreach ($data as $error_descr) $logLine .= "$error_descr\r\n";
        } else $logLine .= "$data\r\n";

        $logLine .= "\r\n";

        fwrite($fp, $logLine);
        fclose($fp);

        if ($die) {

            http_response_code(500);

            if (APP_DEBUG) die($die);
            die('A fatal error has occurred.');
        }
    }


    /**
     * Runtime errors that require immediate stop script
     *
     * @param $data An array or string with log info
     * @return void
     */
    public static function fatal($data, $die = NULL)
    {
        if ($die == NULL) {
            self::add($data, $data, FATAL);
        }
        else self::add($data, $die, FATAL);
    }

    /**
     * System is unusable.
     *
     * @param $data An array or string with log info
     * @return void
     */
    public static function emergency($data)
    {
        self::add($data, FALSE, EMERGENCY);
    }


    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param $data An array or string with log info
     * @return void
     */
    public static function error($data)
    {
        self::add($data, FALSE, ERROR);
    }


    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param $data An array or string with log info
     * @return void
     */
    public static function alert($data)
    {
        self::add($data, FALSE, ALERT);
    }


    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param $data An array or string with log info
     * @return void
     */
    public static function critical($data)
    {
        self::add($data, FALSE, CRITICAL);
    }


    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param $data An array or string with log info
     * @return void
     */
    public static function warning($data)
    {
        self::add($data, FALSE, WARNING);
    }


    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param $data An array or string with log info
     * @return void
     */
    public static function info($data)
    {
        self::add($data, FALSE, INFO);
    }


    /**
     * Detailed debug information.
     *
     * @param $data An array or string with log info
     * @return void
     */
    public static function debug($data)
    {
        self::add($data, FALSE,  DEBUG);
    }


    /**
     * Normal but significant events.
     *
     * @param $data An array or string with log info
     * @return void
     */
    public static function notice($data)
    {
        self::add($data, FALSE,  NOTICE);
    }


    /**
     * Read log file and return as an array of lines
     * @param string $file_path Path to log file
     * @return array|false 
     *         array - An array of strings of log file
     *         false - if empty log file (or log file doesn't exists)
     */
    public static function read($file_path = PATH_APP_LOG)
    {

        if (!is_file($file_path)) return false;

        $lines = file($file_path);

        $log = array();

        foreach ($lines as $line) $log[] = $line;

        return $log;
    }


    /**
     * Delete log file
     * @param string $file Path to log file
     * @return void
     */
    public static function delete($file_path = PATH_APP_LOG)
    {

        if (is_file($file_path)) {
            if (unlink($file_path)) {
                return 200;
            } else return 500;
        }

        return 404;
    }
}
