<?php

namespace Signal\Http;

use Psr\Http\Message\UriInterface;
use Signal\Support\CloneTrait;

class Uri implements UriInterface
{
    use CloneTrait;

    private string $scheme;
    private string $authority;
    private string $userInfo;
    private string $host;
    private ?int $port = null;
    private string $path;
    private string $query;
    private string $fragment;

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getAuthority(): string
    {
        return $this->authority;
    }

    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function withScheme(string $scheme): UriInterface
    {
        return $this->cloneWithProperty('scheme', $scheme);
    }

    public function withUserInfo(string $user, ?string $password = null): UriInterface
    {
        $clone = clone $this;
        $clone->userInfo = $user;

        return $clone;
    }

    public function withHost(string $host): UriInterface
    {
        $clone = clone $this;
        $clone->host = $host;

        return $clone;
    }

    public function withPort(?int $port): UriInterface
    {
        $clone = clone $this;

    }

    public function withPath(string $path): UriInterface
    {
        // TODO: Implement withPath() method.
    }

    public function withQuery(string $query): UriInterface
    {
        // TODO: Implement withQuery() method.
    }

    public function withFragment(string $fragment): UriInterface
    {
        // TODO: Implement withFragment() method.
    }

    public function __toString(): string
    {
        // TODO: Implement __toString() method.
    }
}