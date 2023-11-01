<?php

namespace Signal\Dependency;

use Closure;

class Container implements ContainerInterface
{
    /**
     * @var array
     */
    private array $definitions = [];

    /**
     * @var array
     */
    private array $instances = [];

    /**
     * @var array
     */
    private array $singletons = [];

    /**
     * @var Container|null
     */
    private static ?self $container = null;

    /**
     *
     */
    public function __construct()
    {
        $this->set(ContainerInterface::class, $this);
        static::$container = $this;
    }

    /**
     * @return ContainerInterface
     */
    public static function getContainer(): ContainerInterface
    {
        if (is_null(static::$container)) {
            static::$container = new static();
        }

        return static::$container;
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Invalid resource id: $id");
        }

        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        $instance = $this->resolveInstance($id);

        if (isset($this->singletons[$id])) {
            $this->instances[$id] = $instance;
        }

        return $instance;
    }

    /**
     * @param string $id
     * @return mixed|object
     */
    private function resolveInstance(string $id)
    {
        $definition = $this->definitions[$id];

        if ($definition instanceof Closure) {
            return $definition($this);
        }

        if (is_object($definition)) {
            return $definition;
        }

        if (class_exists($id) && (is_array($definition) || is_null($definition))) {
            return $this->make($id, $definition);
        }

        return $definition;
    }

    /**
     * @param string $class
     * @param array|null $arguments
     * @return object
     */
    public function make(string $class, array $arguments = null): object
    {
        return ClassResolver::for($class)->createInstance($arguments ?? []);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions);
    }

    /**
     * @param string $id
     * @param object|array|null $definition
     * @param bool $singleton
     * @return void
     */
    public function set(string $id, object|array|null $definition = null, bool $singleton = false): void
    {
        $this->definitions[$id] = $definition;

        if ($singleton) {
            $this->singletons[$id] = true;
        }
    }

    /**
     * @param string $id
     * @param object|array|null $definition
     * @return void
     */
    public function singleton(string $id, object|array|null $definition = null): void
    {
        $this->set($id, $definition, true);
    }

    /**
     * @param string $id
     * @return void
     */
    public function forget(string $id): void
    {
        unset($this->definitions[$id], $this->instances[$id], $this->singletons[$id]);
    }
}