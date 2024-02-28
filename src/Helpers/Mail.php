<?php

/**
 * Class-helper for email sending via PHPMailer
 * - Reads all settings from mail config and .env files
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

        // Set generic settings
        $this->isSMTP();
        $this->isHTML(true);
        $this->CharSet = 'UTF-8';
        $this->Encoding = 'base64';

        // Connect PHPMailer config file
        if (env('MAIL_CONFIG')) {
            $file_path =  'App/configs/' . env('MAIL_CONFIG');
            if (is_file($file_path)) $mailConfig = include_once $file_path;
            // Set properties from mail config
            foreach ($mailConfig as $property => $value) $this->$property = $value;
        }

        if (env('MAIL_FROM_ADDRESS') && env('MAIL_FROM_NAME')) 
            $this->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));

        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $this->SMTPDebug = env('MAIL_SMTP_DEBUG') ? env('MAIL_SMTP_DEBUG') : 0;

        // Mail server settings
        if (env('MAIL_HOST')) $this->Host = env('MAIL_HOST');
        if (env('MAIL_PORT')) $this->Port = env('MAIL_PORT');
        if (env('MAIL_SMTP_AUTH')) $this->SMTPAuth = env('MAIL_SMTP_AUTH');
        if (env('MAIL_USERNAME')) $this->Username = env('MAIL_USERNAME');
        if (env('MAIL_PASSWORD')) $this->Password = env('MAIL_PASSWORD');
        if (env('MAIL_ENCRYPTION')) $this->SMTPSecure = env('MAIL_ENCRYPTION');

        // DKIM settings
        // Keys are generated here: https://tools.socketlabs.com/dkim/generator
        // Check DKIM here: https://dmarcly.com/tools/dkim-record-checker
        // Check mail here: https://www.mail-tester.com/
        if (env('DKIM_DOMAIN')) $this->DKIM_domain = env('DKIM_DOMAIN');
        if (env('DKIM_SELECTOR')) $this->DKIM_selector = env('DKIM_SELECTOR');
        if (env('DKIM_IDENTITY')) $this->DKIM_identity = env('DKIM_IDENTITY');
        //'DKIM_private' => 'path/to/your/private.key',
        if (env('DKIM_PRIVATE_KEY')) $this->DKIM_private = env('DKIM_PRIVATE_KEY');
    }
}
