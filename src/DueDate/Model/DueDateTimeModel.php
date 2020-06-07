<?php

declare(strict_types=1);

namespace App\DueDate\Model;

use App\DueDate\Domain\DueDateTime;
use JsonSerializable;

class DueDateTimeModel implements JsonSerializable
{
    private DueDateTime $dueDateTime;

    public function __construct(DueDateTime $dueDateTime)
    {
        $this->dueDateTime = $dueDateTime;
    }

    public function jsonSerialize(): array
    {
        return [
            'dueDateTime' => $this->dueDateTime->format(DueDateTime::RFC3339)
        ];
    }
}
