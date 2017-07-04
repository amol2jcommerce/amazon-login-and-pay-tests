<?php 
require '../vendor/autoload.php';
require_once("../vendor/amzn/amazon-pay-sdk-php/Psr/Log/AbstractLogger.php");
require_once("../vendor/amzn/amazon-pay-sdk-php/Psr/Log/LoggerInterface.php");
require_once("../vendor/amzn/login-and-pay-with-amazon-sdk-php/AmazonPay/Psr/Log/LogLevel.php");
use Psr\Log\LoggerInterface;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class Logger extends \Psr\Log\AbstractLogger {

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        file_put_contents("log.txt", sprintf("%s - %s", $level, $message), FILE_APPEND);
    }
}