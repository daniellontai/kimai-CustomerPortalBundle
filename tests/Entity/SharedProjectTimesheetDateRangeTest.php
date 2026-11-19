<?php

namespace KimaiPlugin\CustomerPortalBundle\tests\Entity;

use KimaiPlugin\CustomerPortalBundle\Entity\SharedProjectTimesheet;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class SharedProjectTimesheetDateRangeTest extends TestCase
{
    public function testFrozenRangeRequiresBothDates(): void
    {
        $portal = new SharedProjectTimesheet();
        $portal->setDateRangeFrozen(true);
        $context = $this->contextExpectingViolation();
        $portal->validateDateRange($context);
    }

    public function testRejectsInvertedDates(): void
    {
        $portal = new SharedProjectTimesheet();
        $portal->setStartDate(new \DateTimeImmutable('2026-10-09'));
        $portal->setEndDate(new \DateTimeImmutable('2026-08-12'));
        $context = $this->contextExpectingViolation();
        $portal->validateDateRange($context);
    }

    private function contextExpectingViolation(): ExecutionContextInterface
    {
        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $builder->expects(self::once())->method('atPath')->willReturnSelf();
        $builder->expects(self::once())->method('addViolation');
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects(self::once())->method('buildViolation')->willReturn($builder);
        return $context;
    }
}
