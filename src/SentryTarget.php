<?php
/**
 * @link http://github.com/darkair/yii2-sentry-log
 * @copyright Copyright (c) 2014
 * @license http://www.yiiframework.com/license/
 */

namespace yii\log;

use Yii;
use yii\base\InvalidConfigException;

/**
 * SentryTarget stores log messages in a sentry
 *
 * @author Dmitry DarkAiR Romanov <darkair@list.ru>
 * @since 1.0
 */
class SentryTarget extends Target
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
        var_dump($this->dsn);
        die;
        $this->client = new \Raven_Client($this->dsn);
    }

    /**
     * Stores log messages to sentry.
     */
    public function export()
    {
        foreach ($this->messages as $message) {
            list($msg, $level, $catagory, $timestamp, $traces) = $message;
            echo '<pre>';
            var_dump($message);
            die;
            /*
            self::$client->captureMessage(
                $errStr,
                array(),
                array(
                    'level' => $errLevel,
                    'extra' => $templateData
                ),
                $isStack
            );*/
        }
    }
}
