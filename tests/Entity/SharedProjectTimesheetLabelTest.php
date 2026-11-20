<?php

/*
 * This file is part of the "Customer-Portal plugin" for Kimai.
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace KimaiPlugin\CustomerPortalBundle\tests\Entity;

use App\Entity\Customer;
use App\Entity\Project;
use KimaiPlugin\CustomerPortalBundle\Entity\SharedProjectTimesheet;
use PHPUnit\Framework\TestCase;

class SharedProjectTimesheetLabelTest extends TestCase
{
    public function testFallsBackToCustomerName(): void
    {
        $portal = new SharedProjectTimesheet();
        $portal->setCustomer($this->customer('Acme GmbH'));

        self::assertSame('Acme GmbH', $portal->getDisplayLabel());
    }

    public function testFallsBackToProjectName(): void
    {
        $portal = new SharedProjectTimesheet();
        $portal->setProject($this->project('Website Relaunch'));

        self::assertSame('Website Relaunch', $portal->getDisplayLabel());
    }

    public function testUsesTrimmedCustomLabelInsteadOfFallback(): void
    {
        $portal = new SharedProjectTimesheet();
        $portal->setCustomer($this->customer('Acme GmbH'));
        $portal->setLabel('  May invoice  ');
        $portal->setStartDate(new \DateTimeImmutable('2026-05-30'));
        $portal->setEndDate(new \DateTimeImmutable('2026-11-19'));

        self::assertSame('May invoice', $portal->getDisplayLabel('05/30/2026 – 11/19/2026'));
    }

    public function testWhitespaceOnlyLabelFallsBackToSourceName(): void
    {
        $portal = new SharedProjectTimesheet();
        $portal->setCustomer($this->customer('Acme GmbH'));
        $portal->setLabel(" \t ");

        self::assertSame('Acme GmbH', $portal->getDisplayLabel());
    }

    public function testAppendsDateRangeOnlyWhenBothBoundariesAreConfigured(): void
    {
        $portal = new SharedProjectTimesheet();
        $portal->setProject($this->project('Website Relaunch'));
        $portal->setStartDate(new \DateTimeImmutable('2026-05-30'));
        $portal->setEndDate(new \DateTimeImmutable('2026-11-19'));

        self::assertSame('Website Relaunch – 05/30/2026 – 11/19/2026', $portal->getDisplayLabel('05/30/2026 – 11/19/2026'));

        $portal->setEndDate(null);

        self::assertSame('Website Relaunch', $portal->getDisplayLabel('05/30/2026 – 11/19/2026'));
    }

    private function customer(string $name): Customer
    {
        return new Customer($name);
    }

    private function project(string $name): Project
    {
        $project = new Project();
        $project->setName($name);

        return $project;
    }
}
