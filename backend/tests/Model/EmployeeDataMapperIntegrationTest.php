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
* Creation Date:      Tue Sep 08 2020
* Last Modified by:   Mario Ravalli
* Last Modified time: Tue Sep 15 2020
*/

declare(strict_types=1);

namespace Neuro3\Presenza\Model;

use Neuro3\Presenza\App;
use PHPUnit\Framework\TestCase;

class EmployeeDataMapperIntegrationTest extends TestCase
{
    /**
     * @var EmployeeDataMapper
     */
    private $SUT;

    protected function setUp(): void
    {
        $app = App::bootstrap();
        $this->SUT = $app->get(EmployeeDataMapper::class);
        $this->SUT->dropSchema();
        $this->SUT->initSchema();
    }

    public function testAddAndFind(): void
    {
        $item = new Employee('foo', 'bar');

        assertNull($this->SUT->find($item->getId()));

        $this->SUT->insert($item);

        assertEquals($item, $this->SUT->find($item->getId()));
    }

    public function testAddAndGetall(): void
    {
        $item1 = new Employee('foo', 'bar');
        $item2 = new Employee('bar', 'foo');

        $this->SUT->insert($item1);
        $this->SUT->insert($item2);

        $items = $this->SUT->getAll();
        assertEquals([ $item1, $item2 ], $items);
    }

    public function testAddUpdateAndFind(): void
    {
        $item = new Employee('foo', 'bar');

        $this->SUT->insert($item);

        $item->updateFromArray([
            'firstname' => 'fooz',
            'lastname' => 'barz',
        ]);

        $this->SUT->update($item);

        assertEquals($item, $this->SUT->find($item->getId()));
    }

    public function testAddAndDelete(): void
    {
        $item = new Employee('foo', 'bar');

        $this->SUT->insert($item);

        assertEquals($item, $this->SUT->find($item->getId()));

        $this->SUT->delete($item->getId());

        assertNull($this->SUT->find($item->getId()));
    }

    public function testFullTextSearch(): void
    {
        $item1 = new Employee('foo', 'bar');
        $item2 = new Employee('bar', 'foo');
        $item3 = new Employee('bar baz', 'foo foz');

        $this->SUT->insert($item1);
        $this->SUT->insert($item2);
        $this->SUT->insert($item3);

        assertEquals([$item1], $this->SUT->getAll('`firstname` = foo'));
        assertEquals([$item2, $item3], $this->SUT->getAll('`firstname` like "%bar%"'));
        assertEquals([$item3], $this->SUT->getAll('`firstname` like "%baz%"'));
    }

    public function testPagination(): void
    {
        $item1 = new Employee('foo', 'bar');
        $item2 = new Employee('bar', 'foo');

        $this->SUT->insert($item1);
        $this->SUT->insert($item2);

        assertEquals([$item1, $item2], $this->SUT->getAll());
        assertEquals([$item2], $this->SUT->getAll('', $page=2, $pageSize=1));
        assertEquals([], $this->SUT->getAll('', $page=2));
    }

    public function testCountPages(): void
    {
        $item1 = new Employee('foo', 'bar');
        $item2 = new Employee('bar', 'baz');
        $item3 = new Employee('bar baz', 'foo foz');
        $item4 = new Employee('bat', 'foz');

        $this->SUT->insert($item1);
        $this->SUT->insert($item2);
        $this->SUT->insert($item3);
        $this->SUT->insert($item4);

        assertSame(1, $this->SUT->countPages());
        assertSame(2, $this->SUT->countPages('`firstname` like "%bar%"', $pageSize=1));
        assertSame(2, $this->SUT->countPages('', $pageSize=2));
    }
}
