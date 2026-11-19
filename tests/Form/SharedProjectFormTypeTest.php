<?php

/*
 * This file is part of the "Customer-Portal plugin" for Kimai.
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace KimaiPlugin\CustomerPortalBundle\tests\Form;

use App\Form\Type\DatePickerType;
use KimaiPlugin\CustomerPortalBundle\Form\SharedProjectFormType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;

class SharedProjectFormTypeTest extends TestCase
{
    public function testDateRangeFieldsUseKimaiNativeDatePicker(): void
    {
        $fields = [];
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->method('add')->willReturnCallback(function (...$arguments) use (&$fields, $builder) {
            if (\is_string($arguments[0])) {
                $fields[$arguments[0]] = $arguments[1];
            }

            return $builder;
        });
        $builder->method('create')->willReturn($builder);

        (new SharedProjectFormType())->buildForm($builder, []);

        self::assertSame(DatePickerType::class, $fields['startDate']);
        self::assertSame(DatePickerType::class, $fields['endDate']);
    }
}
