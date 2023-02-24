<?php

use Pano\Facades\Pano;

Pano::getContexts()
    ->each(fn ($context) => Route::group([], $context->getRoutes()))
;

// Route::group(array_filter([
   //          'as' => $app->getRoute(),
   //          'prefix' => $app->getPath(),
   //          'domain' => $app->getDomain(),
   //          'middleware' => $app->getMiddleware()->push('pano:'.$app->getLocation())->all(),
   //      ]), function () use ($app) {
   //          Route::get('', [ApplicationController::class, 'home'])->name('');

   //          Route::group(['as' => ':'], function () use ($app) {
   //              $app->resources->each(fn ($resource) => static::resource($resource));

   //              $app->dashboards->each(fn ($dash) => static::dashboard($dash));
   //          });

   //          Route::group(['as' => '.'], function () use ($app) {
   //              $app->getApplications()->each(fn ($app) => static::application($app));
   //          });

   //          Route::fallback(fn () => view('Pano::pano'))->name('.404');
   //      });
