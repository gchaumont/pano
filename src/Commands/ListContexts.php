<?php

namespace Pano\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Pano\Facades\Pano;

class ListContexts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pano:contexts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all registered Contexts';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $applications = $this->getContexts(Pano::manager())
            ->map(fn ($app) => [
                'id' => $app->getId(),
                'name' => $app->getName(),
                'location' => $app->getLocation(),
                // 'url' => $app->url(),
                'class' => get_class($app),
            ])
            ->sortBy('location')
        ;

        $this->table(
            ['ID', 'Name', 'Location', 'Url', 'Class'],
            $applications->all()
        );
    }

    public function getContexts($context): Collection
    {
        $contexts = collect($context->getContexts());

        foreach ($contexts as $context) {
            $contexts = $contexts->concat($this->getContexts($context));
        }

        return $contexts->values();
    }
}
