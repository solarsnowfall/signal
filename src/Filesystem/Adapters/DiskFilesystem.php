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
        $this->createDirectoryIfMissing($this->location);
    }

    private function createDirectoryIfMissing(string $location): void
    {
        if (is_dir($location)) {
            return;
        }

        error_clear_last();

        if (!@mkdir($location, '0775', true)) {
            $error = error_get_last();
        }

        clearstatcache(true, $location);

        if (!is_dir($location)) {
            throw new RuntimeException(__METHOD__ . ': ' . ($error['message'] ?? ''));
        }
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

        if (false === @file_put_contents($location, $contents)) {
            throw new RuntimeException(__METHOD__ . ': ' . (error_get_last()['message'] ?? ''));
        }
    }

    public function read(string $path): string
    {
        error_clear_last();
        $contents = @file_get_contents($this->getLocation($path));

        if (false === $contents) {
            throw new RuntimeException(__METHOD__ . ': ' . (error_get_last()['message'] ?? ''));
        }

        return $contents;
    }

    public function delete(string $path): void
    {
        $location = $this->getLocation($path);

        if (!file_exists($location)) {
            return;
        }

        error_clear_last();

        if (!@unlink($location)) {
            throw new RuntimeException(__METHOD__ . ': ' . (error_get_last()['message'] ?? ''));
        }
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

        if (!rmdir($location)) {
            throw new RuntimeException("Unable to delete directory: $location");
        }
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

        error_clear_last();

        if (!@mkdir($location)) {
            throw new RuntimeException(error_get_last()['message'] ?? '');
        }
    }

    public function lastModified(string $path): int|false
    {
        error_clear_last();
        $mtime = @filemtime($this->getLocation($path));

        if (false === $mtime) {
            throw new RuntimeException(error_get_last()['message'] ?? '');
        }

        return $mtime;
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

        if (!@rename($sourceLocation, $destinationLocation)) {
            throw new RuntimeException(error_get_last()['message'] ?? '');
        }
    }

    public function copy(string $source, string $destination): void
    {
        $sourceLocation = $this->getLocation($source);
        $destinationLocation = $this->getLocation($destination);

        if (!@copy($sourceLocation, $destinationLocation)) {
            throw new RuntimeException(error_get_last()['message'] ?? '');
        }
    }

    /**
     * @param string $path
     * @return Generator|SplFileInfo[]
     */
    public function listDirectory(string $path): Generator
    {
        if (!is_dir($path)) {
            return;
        }

        yield from new RecursiveIteratorIterator(
            iterator: new RecursiveDirectoryIterator(
                directory: $path,
                flags: FilesystemIterator::SKIP_DOTS
            ),
            mode: RecursiveIteratorIterator::CHILD_FIRST
        );
    }

    private function getLocation(string $path): string
    {
        if (!str_starts_with($this->location, $path)) {
            $path = $this->location . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
        }

        return $path;
    }
}