<?php
namespace PayWithAmazon;
require_once 'PayWithAmazon/IpnHandler.php';
require_once 'Logger.php';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL);
$f = fopen("IPN.log", "a");

$headers = array_change_key_case(getallheaders());
$body = file_get_contents('php://input');
try{
    $handler = new IpnHandler($headers, $body);
    $logger = new \Psr\Log\Logger();
    $handler->setLogger($logger);
    fwrite($f, $handler->toJson());
} catch (Exception $e){
    fwrite($f,$e->getMessage());
}
