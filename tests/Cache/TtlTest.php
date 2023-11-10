<?php

namespace Cache;

use PHPUnit\Framework\TestCase;
use Signal\Cache\Ttl;

class TtlTest extends TestCase
{
    public function testWithNull()
    {
        $ttl = new Ttl(null);
        $this->assertNull($ttl->getDateTime());
        $this->assertNull($ttl->getSecondsLeft());
        $this->assertNull($ttl->getDateTime());
    }

    public function testWithDateInterval()
    {
        $interval = new \DateInterval('PT1S');
        $ttl = new Ttl($interval);
        $this->assertInstanceOf(\DateInterval::class, $ttl->getDateInterval());
        $this->assertInstanceOf(\DateTime::class, $date = $ttl->getDateTime());
        $this->assertEquals(1, $ttl->getSecondsLeft());
        $now = new \DateTime;
        $now->add($interval);
        $this->assertEquals($now->getTimestamp(), $date->getTimestamp());
    }

    public function testWithInteger()
    {
        $ttl = new Ttl(1);
        $this->assertInstanceOf(\DateInterval::class, $ttl->getDateInterval());
        $this->assertInstanceOf(\DateInterval::class, $ttl->getDateInterval());
        $this->assertInstanceOf(\DateTime::class, $date = $ttl->getDateTime());
        $this->assertEquals(1, $ttl->getSecondsLeft());
        $now = new \DateTime;
        $now->add(new \DateInterval("PT1S"));
        $this->assertEquals($now->getTimestamp(), $date->getTimestamp());
    }

    public function testDateInterval()
    {
        $this->assertNull(Ttl::dateInterval(null));
        $seconds = 1;
        $interval = new \DateInterval("PT{$seconds}S");
        $this->assertEquals($interval, Ttl::dateInterval($interval));
        $this->assertEquals($interval, Ttl::dateInterval($seconds));
    }

    public function testDateTime()
    {
        $this->assertNull(Ttl::dateTime(null));
        $dateTime = new \DateTime;
        $seconds = 1;
        $interval = new \DateInterval("PT{$seconds}S");
        $dateTime->add($interval);
        $this->assertEquals($dateTime->getTimestamp(), Ttl::dateTime($interval)->getTimestamp());
        $this->assertEquals($dateTime->getTimestamp(), Ttl::dateTime($seconds)->getTimestamp());
    }

    public function testSecondsLeft()
    {
        $this->assertNull(Ttl::secondsLeft(null));
        $seconds = 1;
        $interval = new \DateInterval("PT{$seconds}S");
        $this->assertEquals($seconds, Ttl::secondsLeft($seconds));
        $this->assertEquals($seconds, Ttl::secondsLeft($interval));
    }

    public function testTimestamp()
    {
        $this->assertNull(Ttl::timestamp(null));
        $seconds = 1;
        $interval = new \DateInterval("PT{$seconds}S");
        $this->assertEquals(time() + $seconds, Ttl::timestamp($seconds));
        $this->assertEquals(time() + $seconds, Ttl::timestamp($interval));
    }

    public function testExpired()
    {
        $this->assertFalse(Ttl::expired(null));
        $seconds = 1;
        $timestamp = time() + $seconds;
        $date = new \DateTime;
        $date->add(new \DateInterval("PT{$seconds}S"));
        $this->assertFalse(Ttl::expired($timestamp));
        $this->assertFalse(Ttl::expired($date));

        $seconds = 2;
        $timestamp = time() - $seconds;
        $date->sub(new \DateInterval("PT{$seconds}S"));
        $this->assertTrue(Ttl::expired($timestamp), 'with seconds');
        $this->assertTrue(Ttl::expired($date), 'with date');
    }
}