<?php

namespace Neuro3\Presenza\Http\RouteHandler;

use Laminas\Diactoros\ServerRequest;
use Neuro3\Presenza\Model\CalendarDataMapper;
use Neuro3\Presenza\Model\Employee;
use Neuro3\Presenza\Model\JustificationDataMapper;
use Neuro3\Presenza\Model\OfficeDataMapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HomeUnitTest extends TestCase
{
    private Home $SUT;
    /**
     * @var CalendarDataMapper & MockObject
     */
    private CalendarDataMapper $calendarDataMapper;
    /**
     * @var OfficeDataMapper & MockObject
     */
    private OfficeDataMapper $officeDataMapper;
    /**
     * @var JustificationDataMapper & MockObject
     */
    private JustificationDataMapper $justificationDataMapper;

    protected function setUp(): void
    {
        $this->calendarDataMapper = $this->createMock(CalendarDataMapper::class);
        $this->officeDataMapper = $this->createMock(OfficeDataMapper::class);
        $this->justificationDataMapper = $this->createMock(JustificationDataMapper::class);
    }

    public function disabledtestSuccess(): void
    {
        $employee_1 = new Employee('foo', 'bar', 'SHGTQL90D19M269I');
        $employee_2 = new Employee('foo', 'baz', 'SHGTQL90D19M269I');
        $request = new ServerRequest();
        $records = [$employee_1, $employee_2];
    }
}
