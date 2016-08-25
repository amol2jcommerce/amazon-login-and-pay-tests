<?php namespace Psr\Log;
require("PayWithAmazon/Psr/Log/AbstractLogger.php");


class Logger extends AbstractLogger {

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