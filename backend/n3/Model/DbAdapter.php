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
* Creation Date:      Mon Sep 14 2020
* Last Modified by:   Mario Ravalli
* Last Modified time: Wed Sep 30 2020
*/

namespace Neuro3\Model;

use Env\Env;
use mysqli;
use RuntimeException;

class DbAdapter
{
    /**
     * @var MySQLi
     */
    public $link;

    public function __construct()
    {
        $this->link = new MySQLi(Env::get('DB_HOST'), Env::get('DB_USER'), Env::get('DB_PASS'), Env::get('DB_NAME'));
        $this->link->set_charset("utf8");

        // if ($session->has('database')) {
        //     $database = unserialize($session->get('database'));
        //     $this->link = new MySQLi($database['db.host'], $database['db.user'], $database['db.pass'], $database['db.name']);
        //     $this->link->set_charset("utf8");
        // } else {
        //     throw new RuntimeException("Fatal Error: Unable to connect on db", 1);
        // }
    }
}
