<?php

declare(strict_types=1);

namespace App\DueDate\Input;

use DateTime;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

class DueDateTimeInput
{
    private DateTime $submitDateTime;

    private int $turnaroundTime;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $submitDateTime, string $turnaroundTime)
    {
        $submitDateTime = DateTime::createFromFormat(DateTime::RFC3339, $submitDateTime);

        Assert::notFalse($submitDateTime, 'The submitDateTime has to follow the Y-m-d\TH:i:sP format.');
        Assert::integerish($turnaroundTime, 'The turnaroundTime must be an integer. Got: %s.');
        Assert::notEq($turnaroundTime, 0, 'The turnaroundTime can not be 0.');

        $this->submitDateTime = $submitDateTime;
        $this->turnaroundTime = (int) $turnaroundTime;
    }

    public function getSubmitDateTime(): DateTime
    {
        return $this->submitDateTime;
    }

    public function getTurnaroundTime(): int
    {
        return $this->turnaroundTime;
    }
}
