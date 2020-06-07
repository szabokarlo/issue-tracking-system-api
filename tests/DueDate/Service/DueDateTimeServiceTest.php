<?php

declare(strict_types=1);

namespace Tests\DueDate\Service;

use App\DueDate\Domain\DueDateTime;
use App\DueDate\Input\DueDateTimeInput;
use App\DueDate\Service\DueDateTimeService;
use DateTime;
use InvalidArgumentException;
use Tests\TestCase;

class DueDateTimeServiceTest extends TestCase
{
    public function testIfInvalidArgumentExceptionHasThrown(): void
    {
        $submitDateTimeString = '2020-06-07T15:12:38+02:00';
        $submitDateTime       = DateTime::createFromFormat(DateTime::RFC3339, $submitDateTimeString);

        $dueDateInput = $this->createMock(DueDateTimeInput::class);

        $dueDateInput->expects($this->once())
            ->method('getSubmitDateTime')
            ->willReturn($submitDateTime);

        $this->expectException(InvalidArgumentException::class);

        $sut = new DueDateTimeService();

        $sut->calculate($dueDateInput);
    }

    public function testCalculateWorks(): void
    {
        $turnaroundTime       = 1;
        $submitDateTimeString = '2020-06-05T15:12:38+02:00';
        $submitDateTime       = DateTime::createFromFormat(DateTime::RFC3339, $submitDateTimeString);

        $dueDateInput = $this->createMock(DueDateTimeInput::class);

        $dueDateInput->expects($this->once())
            ->method('getSubmitDateTime')
            ->willReturn($submitDateTime);

        $dueDateInput->expects($this->once())
            ->method('getTurnaroundTime')
            ->willReturn($turnaroundTime);

        $sut = new DueDateTimeService();

        $expectedDateTime    = DateTime::createFromFormat(DateTime::RFC3339, '2020-06-05T16:12:38+02:00');
        $expectedDueDateTime = DueDateTime::create($expectedDateTime);

        $this->assertEquals(
            $expectedDueDateTime,
            $sut->calculate($dueDateInput)
        );
    }
}
