<?php
define("PROJECT_ROOT_PATH", __DIR__ . "/../");
 
// include main configuration file
require_once PROJECT_ROOT_PATH . "/inc/config.php";
 
// include the base controller file
require_once PROJECT_ROOT_PATH . "/Controller/Api/BaseController.php";
 
// include the use model file
require_once PROJECT_ROOT_PATH . "/Model/UserModel.php";
require_once PROJECT_ROOT_PATH . "/Model/MaindataModel.php";
require_once PROJECT_ROOT_PATH . "/Model/ApgModel.php";
require_once PROJECT_ROOT_PATH . "/Model/ApgResponModel.php";

require 'PHPMailer2/src/Exception.php';
require 'PHPMailer2/src/PHPMailer.php';
require 'PHPMailer2/src/SMTP.php';
?>