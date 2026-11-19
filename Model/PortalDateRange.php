<?php

/*
 * This file is part of the "Customer-Portal plugin" for Kimai.
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace KimaiPlugin\CustomerPortalBundle\Model;

final class PortalDateRange
{
    public function __construct(private readonly \DateTimeImmutable $start, private readonly \DateTimeImmutable $endExclusive, private readonly bool $legacyMonth = false) {}

    public function getStart(): \DateTimeImmutable { return $this->start; }

    public function getEndExclusive(): \DateTimeImmutable { return $this->endExclusive; }

    public function getStartDate(): \DateTimeImmutable { return $this->start; }

    public function getEndDate(): \DateTimeImmutable { return $this->endExclusive->modify('-1 day'); }

    public function isLegacyMonth(): bool { return $this->legacyMonth; }

    /** @return array{from: string, to: string} */
    public function toQuery(): array { return ['from' => $this->start->format('Y-m-d'), 'to' => $this->getEndDate()->format('Y-m-d')]; }
}
