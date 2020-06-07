<?php

declare(strict_types=1);

namespace Tests\DueDate\Domain;

use App\DueDate\Domain\DueDateTime;
use DateTime;
use InvalidArgumentException;
use Tests\TestCase;

class DueDateTimeTest extends TestCase
{
    /**
     * @dataProvider dataProviderForInvalidArgumentExceptionAtCreation
     */
    public function testIfInvalidArgumentExceptionHasThrownAtCreation(DateTime $dateTime): void
    {
        $this->expectException(InvalidArgumentException::class);

        DueDateTime::create($dateTime);
    }

    public function dataProviderForInvalidArgumentExceptionAtCreation(): array
    {
        return [
            [
                'dateTime' => DateTime::createFromFormat(DateTime::RFC3339, '2020-06-05T08:12:38+02:00'),
            ],
            [
                'dateTime' => DateTime::createFromFormat(DateTime::RFC3339, '2020-06-05T08:17:38+02:00'),
            ],
            [
                'dateTime' => DateTime::createFromFormat(DateTime::RFC3339, '2020-06-07T10:17:38+02:00'),
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForAddTurnaroundTimeWorks
     */
    public function testAddTurnaroundTimeWorks(
        DateTime $dateTime,
        int $turnaroundTimeInHours,
        DueDateTime $expectedDueDateTime
    ) {
        $sut = DueDateTime::create($dateTime);

        $sut->addTurnaroundTime($turnaroundTimeInHours);

        $this->assertEquals(
            $expectedDueDateTime,
            $sut
        );
    }

    public function dataProviderForAddTurnaroundTimeWorks(): array
    {
        return [
            [
                'dateTime'              => DateTime::createFromFormat(DateTime::RFC3339, '2020-06-05T09:12:38+02:00'),
                'turnaroundTimeInHours' => 1,
                'expectedDueDateTime'   => DueDateTime::create(
                    DateTime::createFromFormat(DateTime::RFC3339, '2020-06-05T10:12:38+02:00')
                ),
            ],
            [
                'dateTime'              => DateTime::createFromFormat(DateTime::RFC3339, '2020-06-04T09:12:38+02:00'),
                'turnaroundTimeInHours' => 9,
                'expectedDueDateTime'   => DueDateTime::create(
                    DateTime::createFromFormat(DateTime::RFC3339, '2020-06-05T10:12:38+02:00')
                ),
            ],
            [
                'dateTime'              => DateTime::createFromFormat(DateTime::RFC3339, '2020-06-04T09:12:38+02:00'),
                'turnaroundTimeInHours' => 8,
                'expectedDueDateTime'   => DueDateTime::create(
                    DateTime::createFromFormat(DateTime::RFC3339, '2020-06-05T9:12:38+02:00')
                ),
            ],
            [
                'dateTime'              => DateTime::createFromFormat(DateTime::RFC3339, '2020-06-05T09:12:38+02:00'),
                'turnaroundTimeInHours' => 17,
                'expectedDueDateTime'   => DueDateTime::create(
                    DateTime::createFromFormat(DateTime::RFC3339, '2020-06-09T10:12:38+02:00')
                ),
            ],
        ];
    }
}
