<?php namespace PSR\Log;
require_once("PayWithAmazon/Psr/Log/AbstractLogger.php");
require_once("PayWithAmazon/Psr/Log/LogLevel.php");
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