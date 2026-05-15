<?php

namespace App\Tests\Unit\Validator\Constraints;

use App\Validator\Constraints\NotFutureYear;
use App\Validator\Constraints\NotFutureYearValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

final class NotFutureYearValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): NotFutureYearValidator
    {
        return new NotFutureYearValidator();
    }

    public function testItPassesForCurrentYear(): void
    {
        $this->validator->validate((int) date('Y'), new NotFutureYear());

        $this->assertNoViolation();
    }

    public function testItPassesForPastYear(): void
    {
        $this->validator->validate(2000, new NotFutureYear());

        $this->assertNoViolation();
    }

    public function testItFailsForFutureYear(): void
    {
        $futureYear = (int) date('Y') + 1;

        $this->validator->validate($futureYear, new NotFutureYear());

        $this->buildViolation('The year {{ value }} must not be in the future.')
            ->setParameter('{{ value }}', (string) $futureYear)
            ->assertRaised();
    }

    public function testItPassesForNullValue(): void
    {
        $this->validator->validate(null, new NotFutureYear());

        $this->assertNoViolation();
    }
}
