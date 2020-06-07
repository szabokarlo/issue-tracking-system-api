<?php

declare(strict_types=1);

use App\DueDate\Action\DueDateCalculationAction;
use Slim\App;

return function (App $app) {
    $app->get('/due-date-calculator/{submitDateTime}/{turnaroundTime}', DueDateCalculationAction::class);
};
