<?php
namespace KimaiPlugin\CustomerPortalBundle\Service;
use KimaiPlugin\CustomerPortalBundle\Entity\SharedProjectTimesheet;
use KimaiPlugin\CustomerPortalBundle\Model\PortalDateRange;
use Symfony\Component\HttpFoundation\Request;

final class PortalDateRangeResolver
{
    public function resolve(SharedProjectTimesheet $portal, Request $request, string $timezone): PortalDateRange
    {
        $zone = new \DateTimeZone($timezone);
        if (!$portal->hasDateRangeConfiguration()) {
            $year = max(1, (int) $request->query->get('year', date('Y')));
            $month = min(12, max(1, (int) $request->query->get('month', date('m'))));
            $start = new \DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month), $zone);
            return new PortalDateRange($start, $start->modify('+1 month'), true);
        }
        $from = $portal->isDateRangeFrozen() ? null : $request->query->get('from');
        $to = $portal->isDateRangeFrozen() ? null : $request->query->get('to');
        $start = $from !== null && $from !== '' ? new \DateTimeImmutable((string) $from, $zone) : ($portal->getStartDate() ?? new \DateTimeImmutable('1970-01-01', $zone));
        $end = $to !== null && $to !== '' ? new \DateTimeImmutable((string) $to, $zone) : ($portal->getEndDate() ?? new \DateTimeImmutable('today', $zone));
        if ($start > $end) { throw new \InvalidArgumentException('Start date must not be after end date'); }
        return new PortalDateRange($start->setTime(0, 0), $end->setTime(0, 0)->modify('+1 day'));
    }
}
