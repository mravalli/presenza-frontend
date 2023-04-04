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
* Creation Date:      Wed Sep 09 2020
* Last Modified by:   Mario Ravalli
* Last Modified time: Wed Sep 09 2020
*/
declare(strict_types=1);

namespace Neuro3\Security;

use Serializable;

class Result implements Serializable
{
    const SUCCESS = 1;
    const FAILURE = 0;
    const FAILURE_IDENTITY_NOT_FOUND = -1;
    const FAILURE_IDENTITY_AMBIGUOUS = -2;
    const FAILURE_CREDENTIAL_INVALID = -3;
    const FAILURE_UNCATEGORIZED = -4;

    protected $code;
    protected $identity;
    protected $message;

    /**
     * @param int $code
     * @param mixed $identity
     * @param array $messages
     */
    public function __construct($code, $identity, $messages)
    {
        $this->code = $code;
        $this->identity = $identity;
        $this->messages = $messages;
    }
    
    public function isValid()
    {
        return (self::SUCCESS === $this->code)?true:false;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getIdentity()
    {
        return $this->identity;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function serialize()
    {
        return serialize([
            $this->code,
            $this->identity,
            $this->message,
        ]);
    }

    public function unserialize($data)
    {
        list(
            $this->code,
            $this->identity,
            $this->message,
        ) = unserialize($data);
    }
}
