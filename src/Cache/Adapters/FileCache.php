<?php

namespace Signal\Cache\Adapters;

use DateInterval;
use Generator;
use InvalidArgumentException;
use Signal\Cache\Ttl;
use Signal\Filesystem\Adapters\FilesystemInterface;
use SplFileInfo;

class FileCache extends AbstractAdapter
{
    public function __construct(
        private readonly FilesystemInterface $filesystem
    ) {}

    protected function serializedStorage(): bool
    {
        return true;
    }

    protected function getValue(string $key, mixed $default = null): mixed
    {
        if ($this->keyExists($key)) {
            $raw = $this->filesystem->read($this->getFilename($key));
            $data = json_decode($raw, true);

            return is_null($data['e']) || $data['e'] > time() ? $data['v'] : $default;
        }

        return $default;
    }

    protected function setValue(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $seconds = Ttl::secondsLeft($ttl);

        if (!is_null($seconds) && $seconds < 0) {
            return false;
        }

        $filename = $this->getFilename($key);
        $expiration = !is_null($seconds) ? time() + $seconds : null;
        $value = json_encode(['v' => $value, 'e' => $expiration]);
        $this->filesystem->write($filename, $value);

        return true;
    }

    protected function deleteValue(string $key): bool
    {
        $this->filesystem->delete($this->getFilename($key));

        return true;
    }

    protected function flush(): bool
    {
        foreach ($this->filesystem->listDirectory('') as $file) {
            $this->filesystem->delete($file->getPathname());
        }

        return true;
    }

    protected function keyExists(string $key): bool
    {
        $filename = $this->getFilename($key);

        if ($this->filesystem->fileExists($filename)) {
            $mtime = $this->filesystem->lastModified($filename);

            if ($mtime && time() > $mtime) {
                $this->filesystem->delete($filename);

                return false;
            }

            return true;
        }

        return false;
    }

    private function getFilename(string $key): string
    {
        $hash = hash('sha256', $key);
        $path = str_split(substr($hash, 0, 2));
        $path[] = substr($hash, 2);

        return implode(DIRECTORY_SEPARATOR, $path);
    }

    private function cleanup(): void
    {
        /** @var SplFileInfo $file */
        foreach ($this->filesystem->listDirectory('') as $file) {
            if (time() > $this->filesystem->lastModified($file->getRealPath())) {
                $this->filesystem->delete($file->getRealPath());
            }
        }
    }
}