<?php

/**
 * This file is part of the project "Presenza"
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author:    Mario Ravalli <mario@raval.li>
 * @copyright: Copyright (c) Mario Ravalli <mario@raval.li>
 * @license:   GNU General Public License v3.0 or later
 * 
 * Creation Date: 2020-11-27 09:43:53
 * Modified by:   Mario Ravalli
 * Last Modified: 2021-05-12 23:47:28
 */
declare(strict_types=1);

namespace Neuro3\Response;

use Laminas\Diactoros\Exception;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\InjectContentTypeTrait;
use Laminas\Diactoros\Stream;
use Psr\Http\Message\StreamInterface;

use function get_class;
use function gettype;
use function is_object;
use function is_string;
use function sprintf;

class ExcellResponse extends Response
{
    use InjectContentTypeTrait;

    public function __construct(string $text, int $status = 200, array $headers = [])
    {
        if (!isset($headers['Content-Disposition'])) {
            $headers['Content-Disposition'] = 'attachment; filename=export.xls';
        }
        if (!isset($headers['Pragma'])) {
            $headers['Pragma'] = 'no-cache';
        }
        if (!isset($headers['Expires'])) {
            $headers['Expires'] = 0;
        }
        parent::__construct(
            $this->createBody($text),
            $status,
            $this->injectContentType('application/xls; charset=utf-8', $headers)
        );
    }

    private function createBody(string $text) : StreamInterface
    {
        if ($text instanceof StreamInterface) {
            return $text;
        }

        if (! is_string($text)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid content (%s) provided to %s',
                (is_object($text) ? get_class($text) : gettype($text)),
                __CLASS__
            ));
        }

        $body = new Stream('php://temp', 'wb+');
        $body->write($text);
        $body->rewind();
        return $body;
    }
}
