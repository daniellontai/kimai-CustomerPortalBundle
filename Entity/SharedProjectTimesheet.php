<?php

/*
 * This file is part of the "Customer-Portal plugin" for Kimai.
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace KimaiPlugin\CustomerPortalBundle\Entity;

use App\Entity\Customer;
use App\Entity\Project;
use Doctrine\ORM\Mapping as ORM;
use KimaiPlugin\CustomerPortalBundle\Model\RecordMergeMode;
use KimaiPlugin\CustomerPortalBundle\Repository\SharedProjectTimesheetRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Table(name: 'kimai2_customer_portals')]
#[ORM\Index(columns: ['share_key'])]
#[ORM\Entity(repositoryClass: SharedProjectTimesheetRepository::class)]
#[ORM\UniqueConstraint(columns: ['share_key'])]
#[UniqueEntity(fields: ['shareKey'])]
class SharedProjectTimesheet
{
    public const TYPE_PROJECT = 'project';
    public const TYPE_CUSTOMER = 'customer';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Customer $customer = null;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Project $project = null;

    #[ORM\Column(name: 'share_key', type: 'string', length: 20, nullable: false)]
    #[Assert\Length(max: 20)]
    private ?string $shareKey = null;

    #[ORM\Column(name: 'password', type: 'string', length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $password = null;

    #[ORM\Column(name: 'entry_user_visible', type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $entryUserVisible = false;

    #[ORM\Column(name: 'entry_rate_visible', type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $entryRateVisible = false;

    #[ORM\Column(name: 'record_merge_mode', type: 'string', length: 50, nullable: false)]
    #[Assert\Length(max: 50)]
    private string $recordMergeMode = RecordMergeMode::MODE_NONE;

    #[ORM\Column(name: 'annual_chart_visible', type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $annualChartVisible = false;

    #[ORM\Column(name: 'monthly_chart_visible', type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $monthlyChartVisible = false;

    #[ORM\Column(name: 'budget_stats_visible', type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $budgetStatsVisible = false;

    #[ORM\Column(name: 'time_budget_stats_visible', type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $timeBudgetStatsVisible = false;

    #[ORM\Column(name: 'start_date', type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(name: 'end_date', type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(name: 'freeze_date_range', type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $dateRangeFrozen = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getShareKey(): ?string
    {
        return $this->shareKey;
    }

    public function setShareKey(string $shareKey): void
    {
        $this->shareKey = $shareKey;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function hasPassword(): bool
    {
        return $this->password !== null && $this->password !== '';
    }

    public function setPassword(#[\SensitiveParameter] ?string $password): void
    {
        $this->password = $password;
    }

    public function isEntryUserVisible(): bool
    {
        return $this->entryUserVisible;
    }

    public function setEntryUserVisible(bool $entryUserVisible): void
    {
        $this->entryUserVisible = $entryUserVisible;
    }

    public function isEntryRateVisible(): bool
    {
        return $this->entryRateVisible;
    }

    public function setEntryRateVisible(bool $entryRateVisible): void
    {
        $this->entryRateVisible = $entryRateVisible;
    }

    public function hasRecordMerging(): bool
    {
        return $this->recordMergeMode !== RecordMergeMode::MODE_NONE;
    }

    public function getRecordMergeMode(): string
    {
        return $this->recordMergeMode;
    }

    public function setRecordMergeMode(string $recordMergeMode): void
    {
        $this->recordMergeMode = $recordMergeMode;
    }

    public function isAnnualChartVisible(): bool
    {
        return $this->annualChartVisible;
    }

    public function setAnnualChartVisible(bool $annualChartVisible): void
    {
        $this->annualChartVisible = $annualChartVisible;
    }

    public function isMonthlyChartVisible(): bool
    {
        return $this->monthlyChartVisible;
    }

    public function setMonthlyChartVisible(bool $monthlyChartVisible): void
    {
        $this->monthlyChartVisible = $monthlyChartVisible;
    }

    public function isBudgetStatsVisible(): bool
    {
        return $this->budgetStatsVisible;
    }

    public function setBudgetStatsVisible(bool $budgetStatsVisible): void
    {
        $this->budgetStatsVisible = $budgetStatsVisible;
    }

    public function isTimeBudgetStatsVisible(): bool
    {
        return $this->timeBudgetStatsVisible;
    }

    public function setTimeBudgetStatsVisible(bool $timeBudgetStatsVisible): void
    {
        $this->timeBudgetStatsVisible = $timeBudgetStatsVisible;
    }

    public function getType(): string
    {
        if ($this->customer !== null) {
            return static::TYPE_CUSTOMER;
        }

        return static::TYPE_PROJECT;
    }

    public function getStartDate(): ?\DateTimeImmutable { return $this->startDate; }

    public function setStartDate(?\DateTimeImmutable $startDate): void { $this->startDate = $startDate; }

    public function getEndDate(): ?\DateTimeImmutable { return $this->endDate; }

    public function setEndDate(?\DateTimeImmutable $endDate): void { $this->endDate = $endDate; }

    public function isDateRangeFrozen(): bool { return $this->dateRangeFrozen; }

    public function setDateRangeFrozen(bool $dateRangeFrozen): void { $this->dateRangeFrozen = $dateRangeFrozen; }

    public function hasDateRangeConfiguration(): bool { return $this->startDate !== null || $this->endDate !== null; }

    #[Assert\Callback]
    public function validateDateRange(ExecutionContextInterface $context): void
    {
        if ($this->startDate !== null && $this->endDate !== null && $this->startDate > $this->endDate) {
            $context->buildViolation('shared_project_timesheets.manage.form.date_range_invalid')->atPath('endDate')->addViolation();
        }
        if ($this->dateRangeFrozen && ($this->startDate === null || $this->endDate === null)) {
            $context->buildViolation('shared_project_timesheets.manage.form.date_range_required')->atPath('dateRangeFrozen')->addViolation();
        }
    }
}
