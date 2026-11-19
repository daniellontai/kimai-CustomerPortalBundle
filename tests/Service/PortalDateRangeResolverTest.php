<?php

namespace KimaiPlugin\CustomerPortalBundle\tests\Service;

use KimaiPlugin\CustomerPortalBundle\Entity\SharedProjectTimesheet;
use KimaiPlugin\CustomerPortalBundle\Service\PortalDateRangeResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PortalDateRangeResolverTest extends TestCase
{
    public function testFrozenPortalIgnoresEditedQueryRange(): void
    {
        $portal = new SharedProjectTimesheet();
        $portal->setStartDate(new \DateTimeImmutable('2026-08-12'));
        $portal->setEndDate(new \DateTimeImmutable('2026-09-15'));
        $portal->setDateRangeFrozen(true);

        $range = (new PortalDateRangeResolver())->resolve($portal, new Request(['from' => '2020-01-01', 'to' => '2030-01-01']), 'Europe/Budapest');

        self::assertSame('2026-08-12', $range->getStartDate()->format('Y-m-d'));
        self::assertSame('2026-09-15', $range->getEndDate()->format('Y-m-d'));
    }

    public function testEditablePortalUsesSubmittedRange(): void
    {
        $portal = new SharedProjectTimesheet();
        $portal->setStartDate(new \DateTimeImmutable('2026-08-12'));
        $portal->setEndDate(new \DateTimeImmutable('2026-10-09'));

        $range = (new PortalDateRangeResolver())->resolve($portal, new Request(['from' => '2026-09-16', 'to' => '2026-10-09']), 'Europe/Budapest');

        self::assertSame('2026-09-16', $range->getStartDate()->format('Y-m-d'));
        self::assertSame('2026-10-09', $range->getEndDate()->format('Y-m-d'));
    }

    public function testRejectsMalformedSubmittedDate(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $portal = new SharedProjectTimesheet();
        $portal->setStartDate(new \DateTimeImmutable('2026-08-12'));
        (new PortalDateRangeResolver())->resolve($portal, new Request(['from' => 'not-a-date']), 'Europe/Budapest');
    }

    public function testUsesPortalTimezoneForPersistedDateBoundary(): void
    {
        $portal = new SharedProjectTimesheet();
        $portal->setStartDate(new \DateTimeImmutable('2026-08-12 00:00:00', new \DateTimeZone('UTC')));
        $range = (new PortalDateRangeResolver())->resolve($portal, new Request(), 'Europe/Budapest');
        self::assertSame('Europe/Budapest', $range->getStart()->getTimezone()->getName());
        self::assertSame('2026-08-12 00:00', $range->getStart()->format('Y-m-d H:i'));
    }
}
