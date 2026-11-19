<?php
namespace KimaiPlugin\CustomerPortalBundle\Model;

final class PortalDateRange
{
    public function __construct(private readonly \DateTimeImmutable $start, private readonly \DateTimeImmutable $endExclusive, private readonly bool $legacyMonth = false) {}
    public function getStart(): \DateTimeImmutable { return $this->start; }
    public function getEndExclusive(): \DateTimeImmutable { return $this->endExclusive; }
    public function getStartDate(): \DateTimeImmutable { return $this->start; }
    public function getEndDate(): \DateTimeImmutable { return $this->endExclusive->modify('-1 day'); }
    public function isLegacyMonth(): bool { return $this->legacyMonth; }
    public function toQuery(): array { return ['from' => $this->start->format('Y-m-d'), 'to' => $this->getEndDate()->format('Y-m-d')]; }
}
