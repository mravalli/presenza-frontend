<?php declare(strict_types=1);

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
 * Creation Date: 2020-10-30 09:31:46
 * Modified by:   Mario Ravalli
 * Last Modified: 2021-08-05 18:51:03
 */

namespace Neuro3\Presenza\Model;

use function Neuro3\Presenza\now;
use Assert\Assert;
use Assert\LazyAssertionException;
use DateTimeImmutable;
use Neuro3\Exceptions\InvalidDataException;

class Office extends Model
{
    use ModelTrait;

    protected string $name;
    protected int $color;
    protected string $location;
    protected ?int $managerId = null;
    protected DateTimeImmutable $createdAt;
    protected ?Employee $manager = null;
    protected ?array $employees = null;

    /**
     * @throws InvalidDataException
     */
    public function __construct(
        string $name,
        int $color,
        int $managerId = null,
        string $location = null
    ) {
        $this->createdAt = now();

        $this->validate(compact('name', 'color', 'managerId', 'location'));
        $this->name = $name;
        $this->color = $color;
        $this->managerId = $managerId;
        $this->location = $location;
    }



    public function getName(): string
    {
        return $this->name;
    }
    public function getColor(): int
    {
        return $this->color;
    }
    public function getManagerId(): ?int
    {
        return $this->managerId;
    }
    public function getLocation(): string
    {
        return $this->location;
    }
    public function getManager(): Employee
    {
        return $this->manager;
    }
    public function getEmployees(): ?array
    {
        return $this->employees;
    }

    /**
     * @return mixed[]
     */
    public function jsonSerialize(): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'color'     => $this->color,
            'managerId' => $this->managerId,
            'location'  => $this->location,
            'createdAt' => $this->getCreatedAtAsString(),
            'manager'   => $this->manager,
            'employees' => $this->employees,
        ];
    }

    /**
     * @param mixed[] $data
     * @throws InvalidDataException
     */
    public function updateFromArray(array $data): void
    {
        $this->validate($data);

        $this->name      = $data['name'] ?? $this->name;
        $this->color     = $data['color'] ?? $this->color;
        $this->location  = $data['location'] ?? $this->location;
        $this->managerId = $data['managerId'] ?? $this->managerId;
        
        if (isset($data['manager']) && is_array($data['manager'])) {
            $this->manager = Employee::createFromArray($data['manager'])->withId($this->managerId);
        } elseif (isset($data['manager']) && $data['manager'] instanceof Employee) {
            $this->manager = $data['manager'];
        }
        if (isset($data['employees']) && is_array($data['employees'])) {
            $employees = [];
            foreach ($data['employees'] as $employee) {
                if ($employee instanceof Employee) {
                    $employees[] = $employee;
                } else {
                    $employees[] = Employee::createFromArray($employee)->withId($employee['id']);
                }
            }
            $this->employees = $employees;
        }
    }

    /**
     * @param array $data
     * @throws InvalidDataException
     */
    private function validate(array $data): void
    {
        $assert = Assert::lazy()->tryAll();

        if (isset($data['name'])) {
            $assert->that($data['name'], 'name')->string()->notBlank();
        }
        if (isset($data['color'])) {
            $assert->that($data['color'], 'color')->integer()->min(0)->max(360);
        }
        if (isset($data['location'])) {
            $assert->that($data['location'], 'location')->string()->notBlank();
        }
        if (isset($data['managerId'])) {
            $assert->that($data['managerId'], 'managerId')->integer();
        }

        try {
            $assert->verifyNow();
        } catch (LazyAssertionException $lae) {
            throw InvalidDataException::fromLazyAssertionException($lae);
        }
    }
}
