<?php

declare(strict_types=1);

namespace Tests\DueDate\Action;

use App\DueDate\Domain\DueDateTime;
use App\DueDate\Service\DueDateTimeService;
use App\SlimSkeleton\Actions\ActionPayload;
use DateTime;
use DI\Container;
use Exception;
use Tests\TestCase;

class DueDateCalculationActionTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testActionWorks()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $dateTime    = DateTime::createFromFormat(DateTime::RFC3339, '2020-06-05T16:12:38+02:00');
        $dueDateTime = DueDateTime::create($dateTime);

        $dueDateTimeService = $this->createMock(DueDateTimeService::class);

        $dueDateTimeService->expects($this->once())
            ->method('calculate')
            ->willReturn($dueDateTime);

        $container->set(DueDateTimeService::class, $dueDateTimeService);

        $request  = $this->createRequest('GET', '/due-date-calculator/2020-06-05T15:12:38+02:00/1');
        $response = $app->handle($request);

        $payload           = (string) $response->getBody();
        $expectedPayload   = new ActionPayload(200, ['dueDateTime' => '2020-06-05T16:12:38+02:00']);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}
