<?php declare(strict_types=1);

namespace Neuro3\Presenza\Model;

use Lcobucci\Clock\FrozenClock;
use Lcobucci\Clock\SystemClock;
use PHPUnit\Framework\TestCase;
use function Neuro3\Presenza\now;

class EngagementUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        now(new SystemClock());
    }

    public function testCreatedAt(): void
    {
        $createdAt = new \DateTimeImmutable('@0');
        now(new FrozenClock($createdAt));
        $engagement = new Engagement(1, 1, '2022-01-01', '2022-01-01');
        self::assertEquals($createdAt, $engagement->getCreatedAt());
        $engagement = new Engagement(1, 1, '2022-01-01');
        self::assertEquals($createdAt, $engagement->getCreatedAt());
        $engagement = new Engagement(1, 1, null, '2022-01-01');
        self::assertEquals($createdAt, $engagement->getCreatedAt());
    }
}
