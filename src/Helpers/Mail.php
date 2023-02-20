<?php

/**
 * Class-helper for email sending via PHPMailer
 * - Reads all settings from mail config
 * - Sets sending via SMTP
 * @link https://github.com/PHPMailer/PHPMailer
 * Videna MVC Micro-Framework
 * 
 * @example  
 *   use \Videna\Helpers\Mail;
 *   ...
 *   $mail = new Mail(true);
 *   $mail->send();
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 * 
 */

namespace Videna\Helpers;

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\SMTP;
use \PHPMailer\PHPMailer\Exception;
use \Videna\Core\Log;

class Mail extends PHPMailer
{

    /**
     * Add mail config settings to PHPMailer properties
     * 
     * @param object $exceptions
     * @return void
     */
    public function __construct($exceptions = null)
    {

        // Init PHPMailer first
        parent::__construct($exceptions);

        // Connect app config file
        if (!defined('MAIL_CONFIG')) define('MAIL_CONFIG', 'mail.config.php');
        $file_path =  'App/configs/' . MAIL_CONFIG;
        if (is_file($file_path)) {
            $mailConfig = include_once $file_path;
        } else Log::add(["FATAL ERROR: Mail config file '$file_path' not found"], "FATAL ERROR: Mail config file not found.");

        // Set properties from mail config
        foreach ($mailConfig as $property => $value) $this->$property = $value;
        if (defined('DEF_EMAIL_FROM') and defined('DEF_NAME_FROM')) $this->setFrom(DEF_EMAIL_FROM, DEF_NAME_FROM);

        // Set generic settings
        $this->isSMTP();
        $this->isHTML(true);
        $this->CharSet = 'UTF-8';
        $this->Encoding = 'base64';
    }
}
