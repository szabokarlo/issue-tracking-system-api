<?php

declare(strict_types=1);

namespace App\DueDate\Action;

use App\DueDate\Input\DueDateTimeInput;
use App\DueDate\Model\DueDateTimeModel;
use App\DueDate\Service\DueDateTimeService;
use App\SlimSkeleton\Actions\Action;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class DueDateCalculationAction extends Action
{
    protected DueDateTimeService $dueDateService;

    public function __construct(LoggerInterface $logger, DueDateTimeService $dueDateService)
    {
        parent::__construct($logger);

        $this->dueDateService = $dueDateService;
    }
    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    protected function action(): Response
    {
        $this->logger->info("Due date calculation");

        $submitDate     = (string)$this->resolveArg('submitDateTime');
        $turnaroundTime = (string)$this->resolveArg('turnaroundTime');

        $input = new DueDateTimeInput($submitDate, $turnaroundTime);

        $dueDate = $this->dueDateService->calculate($input);

        $dueDateModel = new DueDateTimeModel($dueDate);

        return $this->respondWithData(
            $dueDateModel
        );
    }
}
