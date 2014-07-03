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
     * Processes the given log messages.
     * This method will filter the given messages with [[levels]] and [[categories]].
     * And if requested, it will also export the filtering result to specific medium (e.g. email).
     * @param array $messages log messages to be processed. See [[Logger::messages]] for the structure
     * of each message.
     * @param boolean $final whether this method is called at the end of the current application
     */
    public function collect($messages, $final)
    {
        $this->messages = array_merge($this->messages, $this->filterMessages($messages, $this->getLevels(), $this->categories, $this->except));
        $count = count($this->messages);
        if ($count > 0 && ($final || $this->exportInterval > 0 && $count >= $this->exportInterval)) {
            $this->export();
            $this->messages = [];
        }
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
            $traces = array_map(
                function($v) {
                    return "{$v['file']}".PHP_EOL."{$v['class']}::{$v['function']} [{$v['line']}]";
                },
                $traces
            );
            if (!empty($traces))
                $options['extra']['traces'] = $traces;

            $this->client->captureMessage(
                $errStr,
                array(),
                $options,
                false
            );
        }
    }
}
