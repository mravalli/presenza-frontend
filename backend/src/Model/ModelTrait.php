<?php

namespace Neuro3\Presenza\Model;

use DateTimeImmutable;
use Doctrine\Instantiator\Exception\ExceptionInterface;
use Doctrine\Instantiator\Instantiator;
use Neuro3\Exceptions\InvalidDataException;
use function Neuro3\Presenza\datetime_from_string;
use function Neuro3\Presenza\now;
use const Neuro3\Presenza\DATETIME_FORMAT;

trait ModelTrait
{
    protected ?int $id = null;
    protected DateTimeImmutable $createdAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $idx): void
    {
        $this->id = $idx;
    }

    public function withId(int $idx): self
    {
        $new = clone $this;
        $new->id = $idx;

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
     * @throws ExceptionInterface
     */
    public static function createFromArray(array $data): self
    {
        /**
         * we use this to avoid double validation.
         * @var self $new
         */
        $new = (new Instantiator)->instantiate(get_called_class());

        $new->createdAt = now();
        $new->updateFromArray($data);

        return $new;
    }
}