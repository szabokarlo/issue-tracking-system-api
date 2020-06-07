<?php

declare(strict_types=1);

namespace Tests\DueDate\Model;

use App\DueDate\Domain\DueDateTime;
use App\DueDate\Model\DueDateTimeModel;
use DateTime;
use Tests\TestCase;

class DueDateTimeModelTest extends TestCase
{
    public function testJsonSerializeWorks(): void
    {
        $dateTimeString = '2020-06-05T15:12:38+02:00';
        $dateTime       = DateTime::createFromFormat(DateTime::RFC3339, $dateTimeString);
        $dueDateTime    = DueDateTime::create($dateTime);

        $sut = new DueDateTimeModel($dueDateTime);

        $this->assertEquals(
            [
                'dueDateTime' => $dateTimeString
            ],
            $sut->jsonSerialize()
        );
    }
}
