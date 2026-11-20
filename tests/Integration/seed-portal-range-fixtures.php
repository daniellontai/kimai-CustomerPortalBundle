<?php

declare(strict_types=1);

/*
 * This file is part of the "Customer-Portal plugin" for Kimai.
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

use App\Entity\Customer;
use App\Entity\Activity;
use App\Entity\Project;
use App\Entity\Timesheet;
use App\Entity\User;
use KimaiPlugin\CustomerPortalBundle\Entity\SharedProjectTimesheet;

require '/opt/kimai/vendor/autoload.php';

$kernel = new App\Kernel('prod', false);
$kernel->boot();
$entityManager = $kernel->getContainer()->get('doctrine')->getManager();
$user = $entityManager->getRepository(User::class)->findOneBy(['username' => 'admin']);
if (!$user instanceof User) { throw new RuntimeException('Missing integration admin'); }
$customer = new Customer('Range fixture customer');
$customer->setTimezone('Europe/Budapest');
$customer->setCountry('HU');
$project = (new Project())->setCustomer($customer)->setName('Range fixture project');
$activity = (new Activity())->setProject($project)->setName('Range fixture activity');
$entityManager->persist($customer);
$entityManager->persist($project);
$entityManager->persist($activity);
$otherCustomer = new Customer('Outside fixture customer');
$otherCustomer->setTimezone('Europe/Budapest');
$otherCustomer->setCountry('HU');
$otherProject = (new Project())->setCustomer($otherCustomer)->setName('Outside fixture project');
$entityManager->persist($otherCustomer);
$entityManager->persist($otherProject);
$entityManager->flush();
foreach ([['2026-08-11', 'Before A'], ['2026-08-12', 'A first boundary'], ['2026-09-15', 'A last boundary'], ['2026-09-16', 'B first boundary'], ['2026-10-09', 'B last boundary'], ['2026-10-10', 'After A']] as [$date, $description]) {
    $begin = new DateTime($date . ' 09:00:00', new DateTimeZone('Europe/Budapest'));
    $timesheet = (new Timesheet())->setUser($user)->setProject($project)->setActivity($activity)->setBegin($begin)->setEnd((clone $begin)->modify('+1 hour'))->setDuration(3600)->setRate(100.0)->setDescription($description)->setTimezone('Europe/Budapest');
    $entityManager->persist($timesheet);
}
foreach ([['batch-a', '2026-08-12', '2026-09-15', true], ['batch-b', '2026-09-16', '2026-10-09', true], ['editable', '2026-08-12', '2026-10-09', false]] as [$key, $start, $end, $frozen]) {
    $portal = new SharedProjectTimesheet();
    $portal->setProject($project);
    $portal->setShareKey($key);
    $portal->setStartDate(new DateTimeImmutable($start));
    $portal->setEndDate(new DateTimeImmutable($end));
    $portal->setDateRangeFrozen($frozen);
    $portal->setEntryRateVisible(true);
    $entityManager->persist($portal);
}
$customerPortal = new SharedProjectTimesheet();
$customerPortal->setCustomer($customer);
$customerPortal->setShareKey('customer-editable');
$customerPortal->setStartDate(new DateTimeImmutable('2026-08-12'));
$customerPortal->setEndDate(new DateTimeImmutable('2026-10-09'));
$entityManager->persist($customerPortal);
$protectedPortal = new SharedProjectTimesheet();
$protectedPortal->setCustomer($customer);
$protectedPortal->setShareKey('protected-customer');
$protectedPortal->setPassword(password_hash('range-password', PASSWORD_DEFAULT));
$entityManager->persist($protectedPortal);
$entityManager->flush();
