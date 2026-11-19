<?php
namespace KimaiPlugin\CustomerPortalBundle\Service;
use KimaiPlugin\CustomerPortalBundle\Entity\SharedProjectTimesheet;
use KimaiPlugin\CustomerPortalBundle\Model\PortalDateRange;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
        $start = $from !== null && $from !== '' ? $this->parseDate((string) $from, $zone) : ($this->configuredDate($portal->getStartDate(), $zone) ?? new \DateTimeImmutable('1970-01-01', $zone));
        $end = $to !== null && $to !== '' ? $this->parseDate((string) $to, $zone) : ($this->configuredDate($portal->getEndDate(), $zone) ?? new \DateTimeImmutable('today', $zone));
        if ($start > $end) { throw new BadRequestHttpException('Start date must not be after end date'); }
        return new PortalDateRange($start->setTime(0, 0), $end->setTime(0, 0)->modify('+1 day'));
    }

    private function parseDate(string $value, \DateTimeZone $timezone): \DateTimeImmutable
    {
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value, $timezone);
        $errors = \DateTimeImmutable::getLastErrors();
        if ($date === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            throw new BadRequestHttpException('Invalid date format, expected Y-m-d');
        }
        return $date;
    }

    private function configuredDate(?\DateTimeImmutable $date, \DateTimeZone $timezone): ?\DateTimeImmutable
    {
        return $date === null ? null : new \DateTimeImmutable($date->format('Y-m-d'), $timezone);
    }
}
