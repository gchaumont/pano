<?php

namespace Pano\Dashboards;

class Home extends Dashboard
{
    public string $icon = 'home';

    public function metrics(): array
    {
        return $this->getApplication()
            ->getResources()
            ->flatMap(fn ($resource) => $resource->metrics())
            ->all()
        ;
    }
}
