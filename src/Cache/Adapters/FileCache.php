<?php

namespace Signal\Cache\Adapters;

use DateInterval;
use FilesystemIterator;
use Generator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Signal\Cache\Ttl;

class FileCache extends AbstractAdapter
{
    private readonly string $directory;

    public function __construct(string $directory = 'cache')
    {
        $this->directory = $this->createDirectory($directory);
    }

    protected function getValue(string $key): mixed
    {
        $filename = $this->getFilename($key);

        if (!file_exists($filename) || $this->keyExpired($key)) {
            return null;
        }

        return file_get_contents($filename);
    }

    private function keyExpired(string $key): bool
    {
        $filename = $this->getFilename($key);
        $modified = $this->getFileModificationTime($filename);

        if ($modified === false) {
            return true;
        }

        $now = time();
        if ($now - $modified < $now) {
            $this->deleteValue($key);
            return true;
        }

        return false;
    }

    protected function setValue(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $filename = $this->getFilename($key);

        if (!file_exists($filename)) {
            $parts = explode(DIRECTORY_SEPARATOR, $filename);
            $path = '';

            while (count($parts) > 1) {
                $path .= array_shift($parts);
                if (!file_exists($path)) {
                    mkdir($path);
                }
                $path .= DIRECTORY_SEPARATOR;
            }
        }

        if (false === @file_put_contents($filename, $value)) {
            return false;
        }

        $seconds = Ttl::secondsLeft($ttl);

        if ($seconds && !@touch($filename, $seconds)) {
            unlink($filename);
            return false;
        }

        return true;
    }

    protected function deleteValue(string $key): void
    {
        @unlink($this->getFilename($key));
    }

    protected function flush(): bool
    {
        $flushed = true;
        foreach ($this->listFiles() as $filename) {
            if (false === $this->deleteValue($filename)) {
                $flushed = false;
            }
        }

        return $flushed;
    }

    protected function keyExists(string $key): bool
    {
        return @file_exists($this->getFilename($key)) && !$this->keyExpired($key);
    }

    private function createDirectory($directory): string
    {
        $path = $directory;

        if (!file_exists($path) && false === @mkdir($path)) {
            throw new InvalidArgumentException("Unable to create cache directory: $path");
        }

        if (!is_writable($path)) {
            throw new InvalidArgumentException("Path can't be written to: $path");
        }

        return $path;
    }

    private function getFilename(string $key): string
    {
        $hash = hash('sha256', $key);
        $path = str_split(substr($hash, 0, 2));
        $path[] = substr($hash, 2);

        return $this->directory . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $path);
    }

    private function getFileModificationTime(string $filename): int|false
    {
        return @filemtime($filename);
    }

    private function listFiles(): Generator
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->directory,
                FilesystemIterator::SKIP_DOTS
            )
        );

        foreach ($iterator as $filename) {
            yield $filename;
        }
    }
}