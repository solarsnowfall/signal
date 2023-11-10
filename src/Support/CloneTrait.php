<?php

namespace Signal\Support;

use http\Message;

trait CloneTrait
{
    public function cloneWithProperty(array|string $property, mixed $value = null): static
    {
        $clone = clone $this;

        if (is_array($property)) {
            $sequential = Arr::isSequential($property);
            $key = $sequential ? $property[0] : key($property);
            $clone->$key = $sequential ? $property[1] : $property[$key];
        } else {
            $clone->$property = $value;
        }

        return $clone;
    }

    public function cloneWithProperties(array $values): static
    {
        $clone = clone $this;

        foreach ($values as $key => $value) {
            $clone->$key = $value;
        }

        return $clone;
    }

    public function cloneWithoutProperty(string $property): static
    {
        $clone = clone $this;
        unset($this->$property);

        return $clone;
    }
}