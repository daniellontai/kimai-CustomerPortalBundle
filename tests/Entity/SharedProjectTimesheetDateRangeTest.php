<?php

namespace KimaiPlugin\CustomerPortalBundle\tests\Entity;

use KimaiPlugin\CustomerPortalBundle\Entity\SharedProjectTimesheet;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class SharedProjectTimesheetDateRangeTest extends TestCase
{
    public function testFrozenRangeRequiresBothDates(): void
    {
        $portal = new SharedProjectTimesheet();
        $portal->setDateRangeFrozen(true);
        $violations = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()->validate($portal);
        self::assertGreaterThan(0, $violations->count());
    }

    public function testRejectsInvertedDates(): void
    {
        $portal = new SharedProjectTimesheet();
        $portal->setStartDate(new \DateTimeImmutable('2026-10-09'));
        $portal->setEndDate(new \DateTimeImmutable('2026-08-12'));
        $violations = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()->validate($portal);
        self::assertGreaterThan(0, $violations->count());
    }
}
