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
        $applications = $this->getApps(resolve(Pano::class));

        $apps = array_map(fn ($app) => [
            'Route' => $app->getAppRoute(),
            $app->appName(),
            $app->getAppUrl(),
            get_class($app),
        ], $applications);

        $this->table(
            ['Route', 'Name', 'Path',  'Class'],
            $apps
        );
    }

    public function getApps($appContainer): array
    {
        $apps = $appContainer->getApplications();
        $allApps = $apps;
        foreach ($apps as $app) {
            array_push($allApps, ...array_values($this->getApps($app)));
        }

        return $allApps;
    }
}
