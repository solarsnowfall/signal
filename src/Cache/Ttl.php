<?php

namespace Signal\Cache;

use DateInterval;
use DateTime;

class Ttl
{
    private ?DateInterval $ttl = null;

    private bool $checkedSecondsLeft = false;

    private ?int $secondsLeft;

    public function __construct(DateInterval|int|null $ttl)
    {
        if ($ttl !== null) {
            $this->ttl = $ttl instanceof DateInterval ? $ttl : $this->secondsToDateInterval($ttl);
        }
    }

    public static function dateInterval(DateInterval|int|null $ttl): ?DateInterval
    {
        return (new self($ttl))->getDateInterval();
    }

    public static function secondsLeft(DateInterval|int|null $ttl): ?int
    {
        return (new self($ttl))->getSecondsLeft();
    }

    public static function timestamp(DateInterval|int|null $ttl): int
    {
        return (new self($ttl))->getSecondsLeft();
    }

    public function getDateInterval(): DateInterval
    {
        return $this->ttl;
    }

    public function getSecondsLeft(): ?int
    {
        if ($this->checkedSecondsLeft) {
            return $this->secondsLeft;
        }

        if ($this->ttl === null) {
            $this->checkedSecondsLeft = true;
            return $this->secondsLeft = null;
        }

        $now = new DateTime;
        $date = clone $now;
        $date->add($this->ttl);
        $this->checkedSecondsLeft = true;

        return $this->secondsLeft = $date->getTimestamp() - $now->getTimestamp();
    }

    private function secondsToDateInterval(int $seconds): DateInterval
    {
        return new DateInterval("PT{$seconds}S");
    }
}