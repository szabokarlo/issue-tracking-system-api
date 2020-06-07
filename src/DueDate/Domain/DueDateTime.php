<?php

declare(strict_types=1);

namespace App\DueDate\Domain;

use DateTime;
use Exception;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

class DueDateTime extends DateTime
{
    private const WORKING_HOUR_START = 9;
    private const WORKING_HOUR_END   = 17;

    private static $workingDays = [1, 2, 3, 4, 5];

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public static function create(DateTime $dateTime)
    {
        $hour = (int)$dateTime->format('H');

        Assert::greaterThanEq(
            $hour,
            self::WORKING_HOUR_START,
            'The hour value of submitDateTime (%s) should be >=' . self::WORKING_HOUR_START . ' and < ' . self::WORKING_HOUR_END . '.'
        );

        Assert::lessThan(
            $hour,
            self::WORKING_HOUR_END,
            'The hour value of submitDateTime (%s) should be >=' . self::WORKING_HOUR_START . ' and < ' . self::WORKING_HOUR_END . '.'
        );

        $dayNumber = (int)$dateTime->format('N');

        Assert::inArray(
            $dayNumber,
            self::$workingDays,
            'The day index value of submitDateTime (%s) should be one from %2$s.'
        );

        return new DueDateTime($dateTime->format(DATE_RFC3339));
    }

    public function addTurnaroundTime(int $turnaroundTimeInHours): self
    {
        $turnaroundWorkingDays  = $this->getTurnaroundWorkingDays($turnaroundTimeInHours);
        $turnaroundWorkingHours = $this->getTurnaroundWorkingHours($turnaroundTimeInHours, $turnaroundWorkingDays);

        return $this->addTurnaroundWorkingDays($turnaroundWorkingDays)
            ->addTurnaroundWorkingHours($turnaroundWorkingHours);
    }

    private function getTurnaroundWorkingDays(int $turnaroundTimeInHours): int
    {
        return (int)($turnaroundTimeInHours / $this->getWorkingHoursPerDay());
    }

    private function getWorkingHoursPerDay(): int
    {
        return self::WORKING_HOUR_END - self::WORKING_HOUR_START;
    }

    private function getTurnaroundWorkingHours(int $turnaroundTimeInHours, int $turnaroundWorkingDays): int
    {
        return $turnaroundTimeInHours - ($turnaroundWorkingDays * $this->getWorkingHoursPerDay());
    }

    private function addTurnaroundWorkingDays(int $turnaroundWorkingDays): self
    {
        if ($turnaroundWorkingDays === 0) {
            return $this;
        }

        $this->modify('+1 day');

        $turnAroundTimeInDays = $this->isWorkingDay()
            ? $turnaroundWorkingDays - 1
            : $turnaroundWorkingDays;

        return $this->addTurnaroundWorkingDays($turnAroundTimeInDays);
    }

    private function isWorkingDay(): bool
    {
        return in_array(
            (int)$this->format('N'),
            self::$workingDays
        );
    }

    private function addTurnaroundWorkingHours(int $turnaroundWorkingHours): self
    {
        if ($turnaroundWorkingHours === 0) {
            return $this;
        }

        if (!$this->isWorkingDay()) {
            $this->modify('+1 day');

            return $this->addTurnaroundWorkingHours($turnaroundWorkingHours);
        }

        $turnaroundTimeInSeconds      = $turnaroundWorkingHours * 60 * 60;
        $totalSecondsUntilTheEndOfDay = $this->getTotalSecondsUntilTheEndOfDay();

        if ($turnaroundTimeInSeconds <= $totalSecondsUntilTheEndOfDay) {
            $remainingTurnaroundWorkingSeconds = $turnaroundTimeInSeconds;
        } else {
            $this->modify('+1 day')
                ->setTimeToTheStartOfTheDay();

            $remainingTurnaroundWorkingSeconds = $turnaroundTimeInSeconds - $totalSecondsUntilTheEndOfDay;
        }

        return $this->addTurnaroundWorkingSeconds($remainingTurnaroundWorkingSeconds);
    }

    private function getTotalSecondsUntilTheEndOfDay(): int
    {
        $endOfTheDay = clone $this;
        $endOfTheDay->setTimeToTheEndOfTheDay();

        $timeUntilTheEndOfDay = $endOfTheDay->diff($this);

        $hoursUntilTheEndOfDay   = (int)$timeUntilTheEndOfDay->format('%h');
        $minutesUntilTheEndOfDay = (int)$timeUntilTheEndOfDay->format('%i');
        $secondsUntilTheEndOfDay = (int)$timeUntilTheEndOfDay->format('%s');

        return ((($hoursUntilTheEndOfDay * 60) + $minutesUntilTheEndOfDay) * 60) + $secondsUntilTheEndOfDay;
    }

    private function setTimeToTheEndOfTheDay(): self
    {
        $this->setTime(self::WORKING_HOUR_END, 0);

        return $this;
    }

    private function setTimeToTheStartOfTheDay(): self
    {
        $this->setTime(self::WORKING_HOUR_START, 0);

        return $this;
    }

    private function addTurnaroundWorkingSeconds(int $turnaroundWorkingSeconds): self
    {
        if ($turnaroundWorkingSeconds === 0) {
            return $this;
        }

        if (!$this->isWorkingDay()) {
            $this->modify('+1 day');

            return $this->addTurnaroundWorkingSeconds($turnaroundWorkingSeconds);
        }

        return $this->modify('+' . $turnaroundWorkingSeconds . ' seconds');
    }
}
