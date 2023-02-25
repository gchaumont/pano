<?php

namespace Pano\Menu\Concerns;

trait HasStatus
{
    public ItemStatus|\Closure $status;

    public function status(ItemStatus|string|\Closure $status): static
    {
        $this->status = is_string($status) ? ItemStatus::from($status) : $status;

        return $this;
    }

    public function getStatus(): null|string
    {
        if (!isset($this->status)) {
            return null;
        }
        if ($this->status instanceof \Closure) {
            return 'go get it';
        }

        return $this->status->name;
    }
}
