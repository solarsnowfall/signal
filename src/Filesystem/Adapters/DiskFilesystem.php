<?php

namespace Signal\Filesystem\Adapters;

use FilesystemIterator;
use Generator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;

class DiskFilesystem implements FilesystemInterface
{
    public function __construct(
        private readonly string $location
    ) {
        if (!empty($this->location)) {
            $this->createDirectoryIfMissing($this->location);
        }
    }

    private function createDirectoryIfMissing(string $location): void
    {
        if (is_dir($location)) {
            return;
        }

        $this->handleError(fn() => mkdir($location, '0775', true));
    }

    public function fileExists(string $path): bool
    {
        return is_file($this->getLocation($path));
    }

    public function directoryExists(string $path): bool
    {
        return is_dir($this->getLocation($path));
    }

    public function write(string $path, string $contents): void
    {
        $location = $this->getLocation($path);
        $this->createDirectoryIfMissing(dirname($location));

        $this->handleError(fn() => file_put_contents($location, $contents));
    }

    public function read(string $path): string
    {
        return $this->handleError(fn() => file_get_contents($this->getLocation($path)));
    }

    public function delete(string $path): void
    {
        $location = $this->getLocation($path);
        echo "$location\n";

        if (!file_exists($location)) {
            return;
        }

        $this->handleError(fn() => unlink($location));

        error_clear_last();
    }

    public function deleteDirectory(string $path): void
    {
        $location = $this->getLocation($path);

        if (!is_dir($location)) {
            return;
        }

        foreach ($this->listDirectory($location) as $file) {
            if (!$this->deleteFile($file)) {
                throw new RuntimeException("Unable to delete file: {$file->getPathname()}");
            }
        }

        $this->handleError(fn() => rmdir($location));
    }

    protected function deleteFile(SplFileInfo $file): bool
    {
        switch ($file->getType()) {
            case 'dir':
                return @rmdir((string) $file->getRealPath());
            case 'link':
                return @unlink($file->getPathname());
            default:
                return @unlink((string) $file->getRealPath());
        }
    }

    public function createDirectory(string $path): void
    {
        $location = $this->getLocation($path);

        if (is_dir($location)) {
            return;
        }

        $this->handleError(fn() => mkdir($this->getLocation($path)));
    }

    public function lastModified(string $path): int|false
    {
        return $this->handleError(fn() => filemtime($this->getLocation($path)));
    }

    public function setLastModified(string $path, ?int $mtime = null)
    {

    }

    public function fileSize(string $path): int
    {
        $location = $this->getLocation($path);
        error_clear_last();

        if (is_file($location) && false !== ($size = @filesize($location))) {
            return $size;
        }

        throw new RuntimeException(error_get_last()['message'] ?? '');
    }

    public function move(string $source, string $destination): void
    {
        $sourceLocation = $this->getLocation($source);
        $destinationLocation = $this->getLocation($destination);

        $this->handleError(fn() => rename($sourceLocation, $destinationLocation));
    }

    public function copy(string $source, string $destination): void
    {
        $sourceLocation = $this->getLocation($source);
        $destinationLocation = $this->getLocation($destination);

        $this->handleError(fn() => copy($sourceLocation, $destinationLocation));
    }

    /**
     * @param string $path
     * @return Generator|SplFileInfo[]
     */
    public function listDirectory(string $path): Generator
    {
        $location = $this->getLocation($path);

        if (!is_dir($location)) {
            return;
        }

        yield from new RecursiveIteratorIterator(
            iterator: new RecursiveDirectoryIterator(
                directory: $location,
                flags: FilesystemIterator::SKIP_DOTS
            ),
            mode: RecursiveIteratorIterator::CHILD_FIRST
        );
    }

    private function getLocation(string $path): string
    {
        if (!str_starts_with($path, $this->location)) {
            $path = $this->location . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
        }

        return $path;
    }

    private function handleError(callable $callback): mixed
    {
        set_error_handler(function(int $code, string $message) {
                throw new \ErrorException($message, $code);
            },
            E_ALL
        );

        $result = $callback();
        restore_error_handler();

        return $result;
    }
}