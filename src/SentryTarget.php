<?php
/**
 * @link http://github.com/darkair/yii2-sentry-log
 * @copyright Copyright (c) 2014
 * @license http://www.yiiframework.com/license/
 */

namespace sentry;

use Yii;
use yii\log;

/**
 * SentryTarget stores log messages in a sentry
 *
 * Stores the message can be a string or an array:
 *  [[msg]] - text message
 *  [[data]] - data for sending to a sentry
 *
 * @author Dmitry DarkAiR Romanov <darkair@list.ru>
 */
class SentryTarget extends yii\log\Target
{
    /**
     * @var string dsn for sentry access
     */
    public $dsn = '';

    /**
     * @var Raven_Client client for working with sentry
     */
    private $client = null;


    /**
     * Initializes the DbTarget component.
     * This method will initialize the [[db]] property to make sure it refers to a valid DB connection.
     * @throws InvalidConfigException if [[db]] is invalid.
     */
    public function init()
    {
        parent::init();
        $this->client = new \Raven_Client($this->dsn);
    }

    /**
     * Stores log messages to sentry.
     */
    public function export()
    {
        foreach ($this->messages as $message) {
            list($msg, $level, $catagory, $timestamp, $traces) = $message;

            $errStr = '';
            $options = [
                'level' => yii\log\Logger::getLevelName($level),
                'extra' => [],
            ];
            $templateData = null;
            if (is_array($msg)) {
                $errStr = isset($msg['msg']) ? $msg['msg'] : '';
                if (isset($msg['data']))
                    $options['extra'] = $msg['data']; 
            } else {
                $errStr = $msg;
            }

            // Store debug trace in extra data
            $options['extra']['traces'] = array_map(
                function($v) {
                    return "{$v['file']} in {$v['class']}::{$v['function']} at line {$v['line']}";
                },
                $traces
            );
            $this->client->captureMessage(
                $errStr,
                array(),
                $options,
                false
            );
        }
    }
}
