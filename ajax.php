<?php
session_name("RANKINGTENISCEPEUSP");
session_start();
require('bootstrap.php');
require_once('config.php');
if ($dev){
    error_reporting(E_ALL); 
    ini_set('display_errors', 1);
}
new controller2();

