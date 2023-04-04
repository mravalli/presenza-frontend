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
 * Creation Date: 2020-11-06 18:57:46
 * Modified by:   Mario Ravalli
 * Last Modified: 2020-11-10 18:40:56
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Model;

use const Neuro3\Presenza\DATE_FORMAT;
use function Neuro3\Presenza\date_from_string;
use function Neuro3\Presenza\now;
use Assert\Assert;
use Assert\LazyAssertionException;
use DateTimeImmutable;
use JsonSerializable;
use Neuro3\Exceptions\InvalidDataException;

class Justification extends Model
{
    use ModelTrait;
    protected string $name;
    protected string $code;
    protected ?int $limit = null;

    /**
     * @throws InvalidDataException
     */
    public function __construct(string $name, string $code)
    {
        $this->validate(compact('name', 'code', 'limit'));

        $this->name = $name;
        $this->code = $code;
        $this->limit = $limit;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function getCode(): string
    {
        return $this->code;
    }
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @return mixed[]
     */
    public function jsonSerialize(): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'code'  => $this->code,
            'limit' => $this->limit,
        ];
    }

    /**
     * @param  mixed[] $data
     * @throws InvalidDataException
     */
    public function updateFromArray(array $data): void
    {
        $data['limit'] = (int) $data['limit'];
        
        $this->validate($data);

        $this->name  = $data['name'] ?? $this->name;
        $this->code  = $data['code'] ?? $this->code;
        $this->limit = $data['limit'] ?? $this->limit;
    }

    /**
     * @param  array $data
     * @throws InvalidDataException
     */
    private function validate(array $data): void
    {
        $assert = Assert::lazy()->tryAll();

        if (isset($data['name'])) {
            $assert->that($data['name'], 'name')->string()->notBlank();
        }
        if (isset($data['code'])) {
            $assert->that($data['code'], 'code')->string()->notBlank();
        }
        if (isset($data['limit'])) {
            $assert->that($data['limit'], 'limit')->nullOr()->integer();
        }
    }
}
