<?php
require_once 'IpnHandler.php';
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
    fwrite($f, $handler->toJson());
} catch (Exception $e){
    fwrite($f,$e->getMessage());
}
