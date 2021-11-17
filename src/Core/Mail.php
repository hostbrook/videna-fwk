<?php
// Videna Framework
// File: /Videna/Core/Mail.php
// Desc: Pre-cooked class to send emails via PHPMailer

// How to use: 
//   use \Videna\Core\Mail;
//   ...
//   $mail = new Mail();
//   $mail->send();

namespace Videna\Core;

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;


class Mail extends PHPMailer {
  
  //public $phpmailer;
  public $header;
  public $footer;

  public function __construct() {

    parent::__construct();

    // Connect app config file
    $file_path =  'App/configs/mail.config.php';
    if ( is_file($file_path) ) {
      $mailConfig = include_once $file_path;
    }
    else Log::add( ["FATAL ERROR: Mail config file '$file_path' not found"], "FATAL ERROR: Mail config file not found.");
    
    // Add default header and footer templates
    $file_path =  'App/Views/mail/header.html';
    $this->header = is_file($file_path) ? file_get_contents($file_path) : '';
    $file_path =  'App/Views/mail/footer.html';
    $this->footer = is_file($file_path) ? file_get_contents($file_path) : '';

    // Create object for PHPMailer
    //$this->phpmailer = new PHPMailer(true);

    // Set generic settings
    $this->isSMTP();
    $this->isHTML(true);
    $this->CharSet = 'UTF-8';
    $this->Encoding = 'base64';

    // Set properties from mail config
    foreach ($mailConfig as $property) $this->$property = $property;
    if ( defined('DEF_EMAIL_FROM') and defined('DEF_NAME_FROM') ) $this->setFrom(DEF_EMAIL_FROM, DEF_NAME_FROM);

  }

}