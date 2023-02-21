<?php

namespace Pano\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Pano\Application\Application;
use Pano\Facades\Pano;

class ListApps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pano:apps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all registered Pano apps.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $applications = $this->getApps(Pano::manager())
            ->map(fn ($app) => [
                'id' => $app->getId(),
                'name' => $app->getName(),
                'location' => $app->getLocation(),
                'url' => $app->url(),
                'class' => get_class($app),
            ])
            ->sortBy('location')
        ;

        $this->table(
            ['ID', 'Name', 'Location', 'Url', 'Class'],
            $applications->all()
        );
    }

    public function getApps($context): Collection
    {
        $apps = collect($context->getContexts());
        foreach ($apps as $app) {
            $apps = $apps->concat($this->getApps($app));
        }

        return $apps->filter(fn ($app) => $app instanceof Application)->values();
    }
}
