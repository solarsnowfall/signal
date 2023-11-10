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

    public static function dateTime(DateInterval|int|null $ttl): ? DateTime
    {
        return (new self($ttl))->getDateTime();
    }

    public static function secondsLeft(DateInterval|int|null $ttl): ?int
    {
        return (new self($ttl))->getSecondsLeft();
    }

    public static function timestamp(DateInterval|int|null $ttl): ?int
    {
       return self::dateTime($ttl)?->getTimestamp() ?? null;
    }

    public static function expired(DateTime|int|null $timestamp): bool
    {
        if (is_null($timestamp)) {
            return false;
        }

        if ($timestamp instanceof DateTime) {
            $timestamp = $timestamp->getTimestamp();
        }

        return time() > $timestamp;
    }

    public function getDateInterval(): ?DateInterval
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
        $date = $this->getDateTime(clone $now);
        $this->checkedSecondsLeft = true;

        return $this->secondsLeft = $date->getTimestamp() - $now->getTimestamp();
    }

    public function getDateTime(?DateTime $now = null): ?DateTime
    {
        if (is_null($this->ttl)) {
            return null;
        }

        if (is_null($now)) {
            $now = new DateTime;
        }

        $now->add($this->ttl);

        return $now;
    }

    private function secondsToDateInterval(int $seconds): DateInterval
    {
        return new DateInterval("PT{$seconds}S");
    }
}