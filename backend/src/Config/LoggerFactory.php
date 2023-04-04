<?php declare(strict_types = 1);

namespace Neuro3\Presenza\Config;

use Env\Env;
use Monolog\Formatter\LineFormatter as MonologLineFormatter;
use Monolog\Handler\FirePHPHandler as MonologFirePHP;
use Monolog\Handler\StreamHandler as MonologStreamHandler;
use Monolog\Logger as Monolog;
use Psr\Log\LoggerInterface as Logger;

class LoggerFactory
{
    public function __invoke(): Logger
    {
        $logger    = new Monolog('');
        $formatter = new MonologLineFormatter("[%datetime%] %level_name%: %message%\n", 'd-M-Y H:i:s');
        $formatter->includeStacktraces();

        $streamHandler = new MonologStreamHandler(__DIR__ . '/../../runtime/logs/base.log', Env::get('DEBUG') ? Monolog::DEBUG : Monolog::INFO);
        $streamHandler->setFormatter($formatter);

        $firePHP = new MonologFirePHP();

        $logger->pushHandler($streamHandler);
        $logger->pushHandler($firePHP);

        return $logger;
    }
}
