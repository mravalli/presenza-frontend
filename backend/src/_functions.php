<?php declare(strict_types=1);
/**
* This file is part of the project: Presenza
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code
*
* @author:    Mario Ravalli <mario@raval.li>
* @copyright: Copyright (c) 2020 Mario Ravalli
* @license:   GNU General Public License v3.0 or later
*
* Creation Date:      Tue Sep 08 2020
* Last Modified by:   Mario Ravalli
* Last Modified time: Tue Sep 15 2020
*/

namespace Neuro3\Presenza;

use Assert\Assert;
use DateTimeImmutable;
use Env\Env;
use ErrorException;
use Neuro3\Exceptions\InvalidDataException;
use Lcobucci\Clock\Clock;
use Lcobucci\Clock\SystemClock;
use Psr\Log\LoggerInterface as Logger;
use Throwable;

/**
 * @return mixed[]
 */
function exception_to_array(Throwable $exception): array
{
    $singleToArray = function (Throwable $exception) {
        $output = [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ];

        if ($exception instanceof InvalidDataException) {
            $output['details'] = $exception->getDetails();
        }

        if (Env::get('DEBUG') === true) {
            $output = array_merge($output, [
                'type' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => explode("\n", $exception->getTraceAsString()),
                'previous' => [],
            ]);
        }

        return $output;
    };

    $result = $singleToArray($exception);
    $last = $exception;

    while ($last = $last->getPrevious()) {
        $result['previous'][] = $singleToArray($last);
    }

    return $result;
}

function php_error_handler(int $errno, string $errstr, string $errfile, int $errline): void
{
    if (! (error_reporting() & $errno)) {
        return; // error_reporting does not include this error
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

function logger(Logger $newLogger = null): Logger
{
    static $logger;

    if ($newLogger) {
        $logger = $newLogger;
    }

    if (! $logger) {
        $logger = (new Config\LoggerFactory())();
    }

    return $logger;
}

function now(Clock $newClock = null): DateTimeImmutable
{
    static $clock;

    if ($newClock) {
        $clock = $newClock;
    }

    if (! $clock) {
        $clock = new SystemClock();
    }

    return $clock->now();
}

function datetime_from_string(string $dateTime): DateTimeImmutable
{
    $dateTime = DateTimeImmutable::createFromFormat(DATETIME_FORMAT, $dateTime);

    Assert::that($dateTime)->notSame(false);

    return $dateTime;
}

function date_from_string(string $date): DateTimeImmutable
{
    $date = DateTimeImmutable::createFromFormat(DATE_FORMAT, $date);

    Assert::that($date)->notSame(false);

    return $date;
}

const DATE_FORMAT = "Y-n-j";
const DATETIME_FORMAT = "Y-m-d\TH:i:s.uO"; // ISO8601 with milliseconds
