<?php

/**
 * Pre-cooked CRON job controller
 * Videna MVC Micro-Framework
 * 
 * @example  
 *   To run in OpenServer:
 *   "%progdir%\modules\php\%phpdriver%\php-win.exe" -c "%progdir%\modules\php\%phpdriver%\php.ini" -q -f "%sitedir%\videna\public\index.php" "Cron@Index" "Argument 2"
 * 
 *   To run at shared linux hosting:
 *   /usr/local/bin/php -q /home/public_html/public/index.php "Cron@Index" "Argument 2"
 *
 *   To direct run at HTTP (admin rights required):
 *   1. Add route to the registered routes list, for example:
 *      Route::add('/cronjob', 'Cron@Index');
 *   2. Log-in in your application as administrator
 *   3. And use http request:
 *      https://domain.com/cronjob?arg1=null&arg2=<arg2>&arg3=<arg3>...&csrf_token=<csrf_token>
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Controllers;

use \Videna\Core\Router;
use \Videna\Core\User;


class CronJob
{

    public function __call($name, $args)
    {

        // Check if Admin runs crone job by HTTP:
        if (Router::$argv[0] === false) {

            // Check if user has admin rights:
            User::detect();

            if (User::get('account') < USR_ADMIN) {
                http_response_code(401);
                exit;
            }
        }

        $method = 'action' . Router::$action;
        call_user_func_array([$this, $method], $args);
    }
}
