<?php

namespace Pano\Menu\Concerns;

enum ItemStatus
{
    case off; // gray
    case info; // blue
    case success; // green
    case danger; // red
    case warning; // orange

    public function status(string $status): static
    {
        $this->status = new ItemStatus($status);

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status->getStatus();
    }
}
