<?php
/**
* This file is part of the project: Neuro3 toolkit
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code
*
* @author:    Mario Ravalli <mario@raval.li>
* @copyright: Copyright (c) 2020 Mario Ravalli
* @license:   GNU General Public License v3.0 or later
*
*
* Creation Date:      Tue Sep 08 2020
* Last Modified by:   Mario Ravalli
* Last Modified time: Wed Oct 07 2020
*/
declare(strict_types=1);

namespace Neuro3\Exceptions;

use Assert\InvalidArgumentException;
use Assert\LazyAssertionException;
use Opis\JsonSchema\ValidationError;
use Opis\JsonSchema\ValidationResult;
use Exception;

final class InvalidDataException extends Exception
{
    /**
     * @var string[]
     */
    private $details = [];

    public static function fromLazyAssertionException(LazyAssertionException $lae): self
    {
        $new = new static('Invalid data', 0, $lae);

        foreach ($lae->getErrorExceptions() as $error) {
            $new->details[$error->getPropertyPath()] = $error->getMessage();
        }

        return $new;
    }

    public static function fromValidationResult(ValidationResult $vr): self
    {
        $new = new static('Invalid data', 0);

        foreach ($vr->getErrors() as $error) {
            $new->details[$error->keyword()] = $error->keywordArgs();
        }

        return $new;
    }

    public static function fromAssertInvalidArgumentException(InvalidArgumentException $iae): self
    {
        $new = new static('Invalid data', 0, $iae);
        $new->details[$iae->getPropertyPath()] = $iae->getMessage();
        return $new;
    }

    /**
     * @return string[]
     */
    public function getDetails(): array
    {
        return $this->details;
    }
}
