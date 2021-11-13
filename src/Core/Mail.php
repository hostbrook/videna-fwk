<?php
// Videna Framework
// File: /Videna/Core/Mail.php
// Desc: Pre-cooked class to send emails via PHPMailer

// Use: 
//   use \Videna\Core\Mail;
//   ...
//   $mail = Mail::$phpmailer;

namespace Videna\Core;

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;


abstract class Mail {
  
  public $phpmailer;
  public $header;
  public $footer;

  public function __construct(){

    // Connect app config file
    $file_path =  'App/configs/mail.config.php';
    if ( is_file($file_path) ) {
      include_once $file_path;
    }
    else Log::add( ["FATAL ERROR" => "Mail config file '$file_path' not found"], "FATAL ERROR: Mail config file not found.");
    
    $file_path =  'App/Views/mail/header.html';
    self::$header = is_file($file_path) ? file_get_contents($file_path) : '';
    $file_path =  'App/Views/mail/footer.html';
    self::$footer = is_file($file_path) ? file_get_contents($file_path) : '';

    self::$phpmailer = new PHPMailer(true);

    self::$phpmailer->isSMTP();

    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    if ( defined('SMTP_DEBUG') )self::$phpmailer->SMTPDebug = SMTP_DEBUG;
    //Ask for HTML-friendly debug output
    //self::$phpmailer->Debugoutput = 'html';
    
    // DKIM settings (if applicaible):
    if ( defined('DKIM_DOMAIN') ) self::$phpmailer->DKIM_domain = DKIM_DOMAIN;
    if ( defined('DKIM_SELECTOR') ) self::$phpmailer->DKIM_selector = DKIM_SELECTOR;
    if ( defined('DKIM_DOMAIN') ) self::$phpmailer->DKIM_identity = EMAIL_FROM;
    if ( defined('DKIM_PRIVATE_KEY') ) self::$phpmailer->DKIM_private_string = DKIM_PRIVATE_KEY;
    // Path to the file with private key:
    //self::$phpmailer->DKIM_private = ''; // 'path/to/your/private.key';
    //self::$phpmailer->DKIM_passphrase = ''; //leave blank if no Passphrase
    
    //Set the hostname of the mail server
    if ( defined('SMTP_HOST') ) self::$phpmailer->Host = SMTP_HOST;
    //Set the SMTP port number - likely to be 25, 465 or 587
    if ( defined('SMTP_PORT') ) self::$phpmailer->Port = SMTP_PORT;
    //Whether to use SMTP authentication
    if ( defined('SMTP_AUTH') ) self::$phpmailer->SMTPAuth = SMTP_AUTH;
    //Username to use for SMTP authentication
    if ( defined('SMTP_USERNAME') ) self::$phpmailer->Username = SMTP_USERNAME;
    //Password to use for SMTP authentication
    if ( defined('SMTP_PASSWORD') ) self::$phpmailer->Password = SMTP_PASSWORD;
    if ( defined('SMTP_SECURE') ) self::$phpmailer->SMTPSecure = SMTP_SECURE;
    
    self::$phpmailer->isHTML(true);
    self::$phpmailer->CharSet = 'UTF-8';
    self::$phpmailer->Encoding = 'base64';
    
    //Set who the message is to be sent from
    if ( defined('EMAIL_FROM') and defined('NAME_FROM') ) self::$phpmailer->setFrom(EMAIL_FROM, NAME_FROM);
    //Set the subject line
    if ( defined('MAIL_SUBJECT') ) self::$phpmailer->Subject = MAIL_SUBJECT;

  } // END __construct


} // END mail.class