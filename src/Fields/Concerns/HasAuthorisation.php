<?php

namespace Pano\Fields\Concerns;

use Closure;

trait HasAuthorisation
{
    public Closure $canSee;

    public function canSee(callable $canSee): static
    {
        $this->canSee = $canSee;

        return $this;
    }

    public function canSeeWhen(string $action, object $authorizable): static
    {
        // Checks the policy
    }
}
