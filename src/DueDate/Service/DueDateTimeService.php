<?php

declare(strict_types=1);

namespace App\DueDate\Service;

use App\DueDate\Domain\DueDateTime;
use App\DueDate\Input\DueDateTimeInput;
use Exception;

class DueDateTimeService
{
    /**
     * @throws Exception
     */
    public function calculate(DueDateTimeInput $dueDateInput): DueDateTime
    {
        $submitDateTime = DueDateTime::create($dueDateInput->getSubmitDateTime());
        $turnaroundTime = $dueDateInput->getTurnaroundTime();

        $dueDateTime = clone $submitDateTime;
        $dueDateTime->addTurnaroundTime($turnaroundTime);

        return $dueDateTime;
    }
}
