<?php

namespace Imdb\Tests;

use Psr\Log\LoggerInterface;

class MemoryLogger implements LoggerInterface
{
    /**
     * @var array 2D string array
     */
    public $logs;

    public function __construct()
    {
        $this->logs = array();
    }

    public function emergency($message, array $context = array())
    {
        $this->log('emergency', $message, $context);
    }

    public function alert($message, array $context = array())
    {
        $this->log('alert', $message, $context);
    }

    public function critical($message, array $context = array())
    {
        $this->log('critical', $message, $context);
    }

    public function error($message, array $context = array())
    {
        $this->log('error', $message, $context);
    }

    public function warning($message, array $context = array())
    {
        $this->log('warning', $message, $context);
    }

    public function notice($message, array $context = array())
    {
        $this->log('notice', $message, $context);
    }

    public function info($message, array $context = array())
    {
        $this->log('info', $message, $context);
    }

    public function debug($message, array $context = array())
    {
        $this->log('debug', $message, $context);
    }

    public function log($level, $message, array $context = array())
    {
        if (isset($this->logs[$level]) || array_key_exists($level, $this->logs))
        {
            array_push($this->logs[$level], [$message, $context]);
        }
        else
        {
            $this->logs[$level][] = [$message, $context];
        }
    }
}
