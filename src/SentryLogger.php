<?php

namespace sentry;

class SentryLogger
{
    // blablanator private static $dsn = 'http://e5057668915545f9bdbe8da22603af60:aa210cc806ef4d66b571e74e874356d9@sentry.gpor.ru/39';
    //private static $dsn = 'http://308ea958bf834ababeb62f07a27e80f9:074b98a23b464d2498ef1ad876df9867@sentry.gpor.ru/40';
    private static $dsn = 'http://c66edff3c6ae4752884380db1ae14e6a:12ab356ca2824ad4a6e68e30af11cde8@sentry.gpor.ru/40';
    private static $env = '';   // строка-идентификатор сайта. Произвольная, для удобства.

    private static $client = null;

    /**
     * Отправка сообщения об ошибке
     * @param string $errStr Сообщение
     * @param array $templateData Переменные, передаваемые в контексте ошибки
     * @param bool $isStack Выводить стек
     * @param $loggerType string Маркировка логгера
     * @return void
     */
    public static function sendError($errStr, $templateData=array(), $isStack=true, $loggerType='php')
    {
        self::sendToSentry($errStr, \Raven_Client::ERROR, $templateData, $isStack, $loggerType);
    }

    /**
     * Отправка сообщения о warning
     * @param string $errStr Сообщение
     * @param array $templateData Переменные, передаваемые в контексте ошибки
     * @param bool $isStack Выводить стек
     * @param string $loggerType Маркировка логгера
     * @return void
     */
    public static function sendWarning($errStr, $templateData=array(), $isStack=false, $loggerType='php')
    {
        self::sendToSentry($errStr, \Raven_Client::WARNING, $templateData, $isStack, $loggerType);
    }

    /**
     * Отправка сообщения об ошибке
     * @param string $errStr Сообщение
     * @param bool $isStack Выводить стек
     * @param string $loggerType Маркировка логгера
     * @return void
     */
    public static function sendDebug($errStr, $isStack=false, $loggerType='php')
    {
        self::sendToSentry($errStr, \Raven_Client::DEBUG, array(), $isStack, $loggerType);
    }

    /**
     * Отправить сообщение в логгер
     * @param string $errStr Сообщение
     * @param string $errLevel Уровень сообщения (error/warning/info/debug etc.)
     * @param array $templateData Переменные, передаваемые в контексте ошибки
     * @param boolean $isStack Выводить стек
     * @param string $loggerType Маркировка логгера
     * @return void
     */
    private static function sendToSentry($errStr, $errLevel=\Raven_Client::ERROR, $templateData=array(), $isStack=true, $loggerType='php')
    {
        if (!self::$client) {
            self::$client = new \Raven_Client(self::$dsn, array(
                'tags' => array(
                    'environment' => self::$env,
                ),
                'logger' => $loggerType
            ));
        }
        self::$client->captureMessage(
            $errStr,
            array(),
            array(
                'level' => $errLevel,
                'extra' => $templateData
            ),
            $isStack
        );
    }
}
