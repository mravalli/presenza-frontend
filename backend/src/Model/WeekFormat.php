<?php
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
 *
 * Creation Date: Thursday October 22nd 2020
 * Modified By:   Thursday October 22nd 2020 10:18:19
 * Last Modified: 2020-11-06 17:54:52
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Model;

use Assert\Assert;
use Assert\LazyAssertionException;
use JsonSerializable;
use Neuro3\Exceptions\InvalidDataException;

class WeekFormat implements JsonSerializable
{
    private string $format;

    /**
     * @throws InvalidDataException
     */
    public function __construct(string $format)
    {
        if (!preg_match('/\d\.\d\d:\d\.\d\d:\d\.\d\d:\d\.\d\d:\d\.\d\d:\d\.\d\d:\d\.\d\d/', $format)) {
            throw new InvalidDataException('Week Malformed' . $format, 10);
        }
        $this->format = $format;
    }

    public function asString(): string
    {
        return $this->format;
    }

    public function asArray(): array
    {
        return \explode(':', $this->format);
    }

    public function jsonSerialize(): array
    {
        return [
            'format' => $this->format,
        ];
    }

    /**
     * @param  array  $week array with single day -> hours
     * @return mixed        WeekFormat
     */
    public static function createFromArray(array $week): ?WeekFormat
    {
        $assert = Assert::lazy()->tryAll();

        $assert->that($week, 'key_mon')->keyExists('mon');
        $assert->that($week, 'key_tue')->keyExists('tue');
        $assert->that($week, 'key_wed')->keyExists('wed');
        $assert->that($week, 'key_thu')->keyExists('thu');
        $assert->that($week, 'key_fri')->keyExists('fri');
        $assert->that($week, 'key_sat')->keyExists('sat');
        $assert->that($week, 'key_sun')->keyExists('sun');

        $assert->that($week['mon'], 'mon')->nullOr()->numeric()->between(0, 8);
        $assert->that($week['tue'], 'tue')->nullOr()->numeric()->between(0, 8);
        $assert->that($week['wed'], 'wed')->nullOr()->numeric()->between(0, 8);
        $assert->that($week['thu'], 'thu')->nullOr()->numeric()->between(0, 8);
        $assert->that($week['fri'], 'fri')->nullOr()->numeric()->between(0, 8);
        $assert->that($week['sat'], 'sat')->nullOr()->numeric()->between(0, 8);
        $assert->that($week['sun'], 'sun')->nullOr()->numeric()->between(0, 8);

        try {
            $assert->verifyNow();
        } catch (LazyAssertionException $lae) {
            throw InvalidDataException::fromLazyAssertionException($lae);
        }

        return new WeekFormat($week['mon'] .':'. $week['tue'] .':'. $week['wed'] .':'. $week['thu'] .':'. $week['fri'] .':'. $week['sat'] .':'. $week['sun']);
    }
}
