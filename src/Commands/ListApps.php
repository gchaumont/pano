<?php

namespace Pano\Commands;

use Illuminate\Console\Command;
use Pano\Pano;

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
        // dd(resolve(Pano::class));
        // dd(collect(resolve(Pano::class)->applications())->map(fn ($app) => $app->getRoute()));
        $applications = $this->getApps(resolve(Pano::class));

        $applications = $applications->map(fn ($app) => [
            'name' => $app->getName(),
            'route' => $app->getRoute(),
            'url' => $app->getAppUrl(),
            'class' => get_class($app),
        ])
            ->sortBy('route')
        ;

        $this->table(
            ['Name', 'Route', 'Url',  'Class'],
            $applications->all()
        );
    }

    public function getApps($appContainer)
    {
        $apps = collect($appContainer->getApplications());

        foreach ($apps as $app) {
            $apps = $apps->concat($this->getApps($app));
        }

        return $apps->values();
    }
}
