<?php

declare(strict_types=1);

namespace Tests\DueDate\Input;

use App\DueDate\Input\DueDateTimeInput;
use DateTime;
use InvalidArgumentException;
use Tests\TestCase;

class DueDateTimeInputTest extends TestCase
{
    /**
     * @dataProvider dataProviderForInvalidArgumentException
     */
    public function testIfInvalidArgumentExceptionHasThrown(string $submitDateTime, string $turnaroundTime): void
    {
        $this->expectException(InvalidArgumentException::class);

        new DueDateTimeInput($submitDateTime, $turnaroundTime);
    }

    public function testGetters(): void
    {
        $submitDateTime = '2020-06-05T15:12:38+02:00';
        $turnaroundTime = '7';

        $dueDateInput = new DueDateTimeInput($submitDateTime, $turnaroundTime);

        $this->assertEquals(
            DateTime::createFromFormat(DateTime::RFC3339, $submitDateTime),
            $dueDateInput->getSubmitDateTime()
        );

        $this->assertEquals(
            (int)$turnaroundTime,
            $dueDateInput->getTurnaroundTime()
        );
    }

    public function dataProviderForInvalidArgumentException(): array
    {
        return [
            [
                'submitDateTime' => 'invalid',
                'turnaroundTime' => 'invalid',
            ],
            [
                'submitDateTime' => '2020-06-05 15:12:38',
                'turnaroundTime' => 'invalid',
            ],
            [
                'submitDateTime' => '2020-06-05T15:12:38+02:00',
                'turnaroundTime' => 'invalid',
            ],
            [
                'submitDateTime' => '2020-06-05T15:12:38+02:00',
                'turnaroundTime' => '0',
            ],
        ];
    }
}
