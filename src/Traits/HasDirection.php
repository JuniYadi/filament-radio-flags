<?php

namespace JuniYadi\RadioFlags\Traits;

use Closure;

trait HasDirection
{
    protected string|Closure|null $direction = null;

    public function direction(string|Closure|null $direction): static
    {
        $this->direction = $direction;

        return $this;
    }

    public function getDirection(): ?string
    {
        return $this->evaluate($this->direction);
    }
}
