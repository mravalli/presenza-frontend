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
* Creation Date:      Tue Oct 13 2020
* Last Modified by:   Mario Ravalli
* Last Modified time: Tue Oct 13 2020
*/
declare(strict_types=1);

namespace Neuro3\Presenza\Model;

use const Neuro3\Presenza\DATETIME_FORMAT;
use function Neuro3\Presenza\datetime_from_string;
use function Neuro3\Presenza\now;
use Doctrine\Instantiator\Instantiator;
use Neuro3\Exceptions\InvalidDataException;
use Opis\JsonSchema\Validator;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\Schema;

/**
 * Common Method
 */
trait CommonMethod
{
    public function getId(): int
    {
        return $this->id;
    }

    public function withId(int $id): self
    {
        $new = clone $this;
        $new->id = $id;

        return $new;
    }

    public function withCreatedAt(string $createdAt): self
    {
        $new = clone $this;
        $new->createdAt = datetime_from_string($createdAt);

        return $new;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getCreatedAtAsString(): string
    {
        return $this->createdAt->format(DATETIME_FORMAT);
    }

    /**
     * This method is intended as a type-unsafe alternative to the constructor
     *
     * @param mixed[] $data
     *
     * @throws InvalidDataException
     */
    public static function createFromArray(array $data): self
    {
        /**
         * we use this to avoid double validation.
         * @var self $new
         */
        $new = (new Instantiator)->instantiate(__CLASS__);

        $new->createdAt = now();
        $new->updateFromArray($data);

        return $new;
    }

    /**
     * @param mixed[] $data
     *
     * @throws InvalidDataException
     */
    protected function validate(array $data): void
    {
        $data = (object) $data;
        $schema = Schema::fromJsonString(\file_get_contents(__DIR__ . '/../Schema/' . self::SCHEMA_VALIDATION));
        $validator = new Validator();

        /** @var ValidationResult $result */
        $result = $validator->schemaValidation($data, $schema);

        if (!$result->isValid()) {
            //print_r($result);
            throw InvalidDataException::fromValidationResult($result);
        }
    }
}
