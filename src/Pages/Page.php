<?php

namespace Pano\Pages;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Pano\Application\Application;
use Pano\Components\Component;
use Pano\Concerns\HasBreadcrumbs;
use Pano\Concerns\Routable;
use Pano\Controllers\PageController;
use Pano\Dashboards\Dashboard;
use Pano\Resource\Resource;

/**
 * Part of the User Interface
 * reachable by Url.
 */
class Page extends Component
{
    use Routable;
    use HasBreadcrumbs;

    public function __construct(string $name)
    {
        $this->name($name);
        $this->route($name);
        $this->id($name);
    }

    public function toResponse($request): JsonResponse
    {
    }

    public function breadcrumbs(): array
    {
        $app = $this;
        $crumbs = collect();
        while ($app = $app->getApplication()) {
            $crumbs->prepend([
                'name' => $app->getName(),
                'url' => $app->url(),
            ]);
        }

        return $crumbs->all();
    }

    public function definition()
    {
        if (($app = $this->getApplication()) && $app->url() != $app->homepage() && $app->homepage() == $this->url()) {
            $alias = [$app->url()];
        }

        return [
            '@type' => 'Page',
            ...parent::definition(),
            'name' => $this->getLocation(),
            'path' => $this->url(),
            'alias' => $alias ?? [],
            'breadcrumbs' => $this->getBreadcrumbs(),
            'children' => $this->getChildren()
                ->map(fn ($child) => $child->definition())
                ->values(),
        ];
    }

    public function registerRoutes(): void
    {
        Route::group(array_filter([
            'middleware' => $this->getMiddleware()->push('pano:'.$this->getLocation())->all(),
            'as' => $this->getContextSeparator().$this->getId(),
            'domain' => $this->getDomain(),
            'prefix' => $this->getPath(),
        ]), function () {
            if ($this instanceof Page) {
                // dump($this->getId());
                Route::get('', PageController::class)->name('');
            }

            if ($this instanceof Resource) {
                Route::group(['prefix' => '_endpoints'], function () {
                    $this->getEndpoints()->each(fn ($endpoint) => $endpoint->registerRoute());
                    $this->getMetrics()->each(fn ($metric) => $metric->registerRoute());
                });
            } elseif ($this instanceof Dashboard) {
                Route::group(['prefix' => '_endpoints'], function () {
                    $this->getMetrics()->each(fn ($metric) => $metric->registerRoute());
                });
            }
            Route::controller(PageController::class)
                ->group(
                    fn () => $this->getContexts()
                        ->filter(fn ($item) => is_a($item, Component::class))
                        ->each(fn ($item) => $item instanceof Page
                               && Route::get($item->getPath())->name($item->getContextSeparator().$item->getRoute()))
                        ->each(fn (Component $item) => $item->registerRoutes())
                )
            ;
            if ($this instanceof Application) {
                // if ($this->url() !== $this->homepage()) {
                //     Route::redirect($this->url(), $this->homepage());
                // }
                // Route::fallback(fn () => redirect($this->getPages()->first()->getPath()))->name($this->getContextSeparator().'404');
            }
        });
    }
}
