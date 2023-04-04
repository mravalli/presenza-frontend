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
* Creation Date:      Wed Sep 09 2020
* Last Modified by:   Mario Ravalli
* Last Modified time: Wed Sep 09 2020
*/
declare(strict_types=1);

namespace Neuro3\Security;

class User
{
    const ADMIN      = 1;
    const OPERATOR   = 2;
    const TECHNICIAN = 3;

    private $id;
    private $username;
    private $role;
    private $email;
    private $fullname;
    private $path;
    private $language;
    private $expire;

    public function __construct(array $attributes)
    {
        if (empty($attributes['username'])) {
            throw new \InvalidArgumentException('No username provided.');
        }
 
        $this->id = $attributes['id'];
        $this->username = $attributes['username'];
        $this->role = (int) $attributes['role'];
        $this->email = $attributes['email'];
        $this->fullname = $attributes['fullname'];
        $this->path = $attributes['path'];
        $this->language = $attributes['language'];
        $this->expire = $attributes['expire'];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }
 
    public function getRole()
    {
        return $this->role;
    }
 
    public function getSalt()
    {
        return '';
    }
 
    public function eraseCredentials()
    {
    }

    public function isAdmin() : bool
    {
        return ($this->role === self::ADMIN)?true:false;
    }
}
