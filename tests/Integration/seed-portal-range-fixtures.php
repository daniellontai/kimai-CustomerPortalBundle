<?php

declare(strict_types=1);

use App\Entity\Customer;
use App\Entity\Project;
use App\Entity\Timesheet;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KimaiPlugin\CustomerPortalBundle\Entity\SharedProjectTimesheet;

require '/opt/kimai/vendor/autoload.php';

$kernel = new App\Kernel('prod', false);
$kernel->boot();
$entityManager = $kernel->getContainer()->get(EntityManagerInterface::class);
$user = $entityManager->getRepository(User::class)->findOneBy(['username' => 'admin']);
if (!$user instanceof User) { throw new RuntimeException('Missing integration admin'); }
$customer = new Customer('Range fixture customer');
$customer->setTimezone('Europe/Budapest');
$project = (new Project())->setCustomer($customer)->setName('Range fixture project');
$entityManager->persist($customer); $entityManager->persist($project);
foreach ([['2026-08-11', 'Before A'], ['2026-08-12', 'A first boundary'], ['2026-09-15', 'A last boundary'], ['2026-09-16', 'B first boundary'], ['2026-10-09', 'B last boundary'], ['2026-10-10', 'After A']] as [$date, $description]) {
    $begin = new DateTime($date . ' 09:00:00', new DateTimeZone('Europe/Budapest'));
    $timesheet = (new Timesheet())->setUser($user)->setProject($project)->setBegin($begin)->setEnd((clone $begin)->modify('+1 hour'))->setDuration(3600)->setRate(100.0)->setDescription($description)->setTimezone('Europe/Budapest');
    $entityManager->persist($timesheet);
}
foreach ([['batch-a', '2026-08-12', '2026-09-15', true], ['batch-b', '2026-09-16', '2026-10-09', true], ['editable', '2026-08-12', '2026-10-09', false]] as [$key, $start, $end, $frozen]) {
    $portal = new SharedProjectTimesheet(); $portal->setProject($project); $portal->setShareKey($key); $portal->setStartDate(new DateTimeImmutable($start)); $portal->setEndDate(new DateTimeImmutable($end)); $portal->setDateRangeFrozen($frozen); $portal->setEntryRateVisible(true); $entityManager->persist($portal);
}
$entityManager->flush();
