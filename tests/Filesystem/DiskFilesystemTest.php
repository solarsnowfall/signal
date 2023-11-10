<?php

namespace Filesystem;

use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Signal\Filesystem\Adapters\DiskFilesystem;
use Signal\Filesystem\Adapters\FilesystemInterface;
use SplFileInfo;

class DiskFilesystemTest extends TestCase
{
    const TEST_LOCATION = 'tests/Filesystem/disk';

    private FilesystemInterface $filesystem;

    public function setUp(): void
    {
        parent::setUp();
        $this->filesystem = new DiskFilesystem(self::TEST_LOCATION);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $directory = 'tests/Filesystem/disk';

        $iterator = new RecursiveIteratorIterator(
            iterator: new RecursiveDirectoryIterator(
                directory: $directory,
                flags: FilesystemIterator::SKIP_DOTS
            ),
            mode: RecursiveIteratorIterator::CHILD_FIRST
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            $file->isFile()
                ? unlink($file->getRealPath())
                : rmdir($file->getRealPath());
        }

        rmdir($directory);
    }

    public function testFileDoesNotExist()
    {
        $this->assertFalse($this->filesystem->fileExists('test.txt'));
    }

    public function testDirectoryDoesNotExist()
    {
        $this->assertFalse($this->filesystem->directoryExists('test'));
    }

    public function testWrite()
    {
        $exception = null;

        try {
            $this->filesystem->write('test.txt', 'test text');
        } catch (\Throwable $throwable) {
            $exception = $throwable;
        }

        $this->assertNull($exception);
    }

    public function testFileExists()
    {
        $this->filesystem->write('test.txt', 'test text');
        $this->assertTrue($this->filesystem->fileExists('test.txt'));
    }

    public function testCreateDirectory()
    {
        $exception = null;

        try {
            $this->filesystem->createDirectory('test');
        } catch (\Throwable $throwable) {
            $exception = $throwable;
        }

        $this->assertNull($exception);
    }

    public function testDirectoryExists()
    {
        $this->filesystem->createDirectory('test');
        $this->assertTrue($this->filesystem->directoryExists('test'));
    }

    public function testRead()
    {
        $this->filesystem->write('test.txt', 'test text');
        $contents = $this->filesystem->read('test.txt');
        $this->assertEquals('test text', $contents);
    }

    public function testDelete()
    {
        $this->filesystem->write('test.txt', 'test text');
        $exception = null;

        try {
            $this->filesystem->delete('test.txt');
        } catch (\Throwable $throwable) {
            $exception = $throwable;
        }

        $this->assertNull($exception);
    }

    public function testDeleteDirectory()
    {
        $this->filesystem->write('test/test.txt', 'test text');
        $exception = null;

        try {
            $this->filesystem->deleteDirectory('test');
        } catch (\Throwable $throwable) {
            $exception = $throwable;
        }

        $this->assertNull($exception);
    }

    public function testLastModified()
    {
        $time = time();
        $this->filesystem->write('test.txt', 'test text');
        $this->assertEquals($time, $this->filesystem->lastModified('test.txt'));
    }

    public function testFileSize()
    {
        $this->filesystem->write('test.txt', 'test text');
        $this->assertEquals(9, $this->filesystem->fileSize('test.txt'));
    }

    public function testMove()
    {
        $this->filesystem->write('test.txt', 'test text');
        $exception = null;

        try {
            $this->filesystem->move('test.txt', 'toast.txt');
        } catch (\Throwable $throwable) {
            $exception = $throwable;
        }

        $this->assertNull($exception);
        $from = self::TEST_LOCATION . DIRECTORY_SEPARATOR . 'test.txt';
        $this->assertFalse(file_exists($from));
        $to = self::TEST_LOCATION . DIRECTORY_SEPARATOR . 'toast.txt';
        $this->assertTrue(file_exists($to));
    }

    public function testCopy()
    {
        $this->filesystem->write('test.txt', 'test text');
        $exception = null;

        try {
            $this->filesystem->copy('test.txt', 'copied.txt');
        } catch (\Throwable $throwable) {
            $exception = $throwable;
        }

        $this->assertNull($exception);
        $from = self::TEST_LOCATION . DIRECTORY_SEPARATOR . 'test.txt';
        $this->assertTrue(file_exists($from));
        $to = self::TEST_LOCATION . DIRECTORY_SEPARATOR . 'copied.txt';
        $this->assertTrue(file_exists($to));
    }
}