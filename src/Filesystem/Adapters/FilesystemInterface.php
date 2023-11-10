<?php

namespace Signal\Filesystem\Adapters;

use Generator;

interface FilesystemInterface
{
    public function fileExists(string $path): bool;

    public function directoryExists(string $path): bool;

    public function write(string $path, string $contents): void;

    //public function writeStream(string $path, $contents): void;

    public function read(string $path): string;

    //public function readStream(string $path);

    public function delete(string $path): void;

    public function deleteDirectory(string $path): void;

    public function createDirectory(string $path): void;

    //public function setVisibility(string $path, string $visibility): void;

    //public function visibility(string $path): void;

    //public function mimeType(string $path)

    public function lastModified(string $path): int|false;

    public function fileSize(string $path): int;

    //public function listContents(string $path, bool $deep): iterable;

    public function move(string $source, string $destination): void;

    public function copy(string $source, string $destination): void;

    public function listDirectory(string $path): Generator;
}