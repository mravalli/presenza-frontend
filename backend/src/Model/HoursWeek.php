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
 * Modified By:   Thursday October 22nd 2020 09:35:56
 * Last Modified: 2020-11-06 17:54:25
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Model;

use Assert\Assert;
use Assert\LazyAssertionException;
use function Neuro3\Presenza\now;
use DateTimeImmutable;
use JsonSerializable;
use Neuro3\Exceptions\InvalidDataException;

class HoursWeek extends Model
{
    const FULLTIME = "FT";
    const PARTTIME = "PT";

    use ModelTrait;
    protected string $type;
    protected float $totalHours;
    protected int $days;
    protected float $mon;
    protected float $tue;
    protected float $wed;
    protected float $thu;
    protected float $fri;
    protected float $sat;
    protected float $sun;

    /**
     * @throws InvalidDataException
     */
    public function __construct(
        string $type,
        int $days,
        float $mon,
        float $tue,
        float $wed,
        float $thu,
        float $fri,
        float $sat,
        float $sun
    ) {
        $this->validate(compact(
            'type',
            'days',
            'mon',
            'tue',
            'wed',
            'thu',
            'fri',
            'sat',
            'sun'
        ));

        $this->type = $type;
        $this->days = $days;
        $this->mon  = $mon;
        $this->tue  = $tue;
        $this->wed  = $wed;
        $this->thu  = $thu;
        $this->fri  = $fri;
        $this->sat  = $sat;
        $this->sun  = $sun;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDays(): int
    {
        return $this->days;
    }

    public function getMon(): float
    {
        return (float) $this->mon;
    }

    public function getTue(): float
    {
        return $this->tue;
    }
    
    public function getWed(): float
    {
        return $this->wed;
    }
    
    public function getThu(): float
    {
        return $this->thu;
    }
    
    public function getFri(): float
    {
        return $this->fri;
    }
    
    public function getSat(): float
    {
        return $this->sat;
    }
    
    public function getSun(): float
    {
        return $this->sun;
    }

    public function getTotalHours(): float
    {
        return $this->totalHours;
    }

    /**
     * @return mixed[]
     */
    public function jsonSerialize(): array
    {
        return [
            'id'         => $this->id,
            'type'       => $this->type,
            'days'       => $this->days,
            'totalHours' => $this->totalHours,
            'mon'        => $this->mon,
            'tue'        => $this->tue,
            'wed'        => $this->wed,
            'thu'        => $this->thu,
            'fri'        => $this->fri,
            'sat'        => $this->sat,
            'sun'        => $this->sun,
            'createdAt'  => $this->getCreatedAtAsString()
        ];
    }

    /**
     * TODO: Refactoring totalHours and format
     * @param mixed[] $data
     * @throws InvalidDataException
     */
    public function updateFromArray(array $data): void
    {
        $data['days'] = (int) $data['days'];
        $data['mon']  = (float) $data['mon'];
        $data['tue']  = (float) $data['tue'];
        $data['wed']  = (float) $data['wed'];
        $data['thu']  = (float) $data['thu'];
        $data['fri']  = (float) $data['fri'];
        $data['sat']  = (float) $data['sat'];
        $data['sun']  = (float) $data['sun'];

        $this->validate($data);

        $this->type       = $data['type'] ?? $this->type;
        $this->days       = $data['days'] ?? $this->days;
        $this->totalHours = $data['mon'] + $data['tue'] + $data['wed'] + $data['thu'] + $data['fri'] + $data['sat'] + $data['sun'];
        $this->mon        = $data['mon'] ?? $this->mon;
        $this->tue        = $data['tue'] ?? $this->tue;
        $this->wed        = $data['wed'] ?? $this->wed;
        $this->thu        = $data['thu'] ?? $this->thu;
        $this->fri        = $data['fri'] ?? $this->fri;
        $this->sat        = $data['sat'] ?? $this->sat;
        $this->sun        = $data['sun'] ?? $this->sun;
    }

    /**
     * @param mixed[] $data
     * @throws InvalidDataException
     */
    private function validate(array $data): void
    {
        $assert = Assert::lazy()->tryAll();

        if (isset($data['days'])) {
            $assert->that($data['days'], 'days')->notEmpty()->integer();
        }

        if (isset($data['type'])) {
            $assert->that($data['type'], 'type')->notEmpty()->string()->notBlank();
        }

        $assert->that($data, 'key_mon')->keyExists('mon');
        $assert->that($data, 'key_tue')->keyExists('tue');
        $assert->that($data, 'key_wed')->keyExists('wed');
        $assert->that($data, 'key_thu')->keyExists('thu');
        $assert->that($data, 'key_fri')->keyExists('fri');
        $assert->that($data, 'key_sat')->keyExists('sat');
        $assert->that($data, 'key_sun')->keyExists('sun');

        $assert->that($data['mon'], 'mon')->nullOr()->numeric()->between(0, 8);
        $assert->that($data['tue'], 'tue')->nullOr()->numeric()->between(0, 8);
        $assert->that($data['wed'], 'wed')->nullOr()->numeric()->between(0, 8);
        $assert->that($data['thu'], 'thu')->nullOr()->numeric()->between(0, 8);
        $assert->that($data['fri'], 'fri')->nullOr()->numeric()->between(0, 8);
        $assert->that($data['sat'], 'sat')->nullOr()->numeric()->between(0, 8);
        $assert->that($data['sun'], 'sun')->nullOr()->numeric()->between(0, 8);

        try {
            $assert->verifyNow();
        } catch (LazyAssertionException $lae) {
            throw InvalidDataException::fromLazyAssertionException($lae);
        }
    }
}
