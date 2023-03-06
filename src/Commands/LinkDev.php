<?php

namespace Pano\Commands;

use Illuminate\Console\Command;

class LinkDev extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pano:dev:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create symlinks for development';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $target = base_path('vendor/gchaumont/pano/public');
        $link = public_path('/vendor/pano');
        if (is_link($link)) {
            app()->make('files')->delete($link);
        } else {
            app()->make('files')->deleteDirectory($link);
        }
        app()->make('files')->link($target, $link);

        // $target = base_path('vendor/gchaumont/pano/resources');
        // $link = resource_path('/vendor/pano');
        // if (is_link($link)) {
        //     app()->make('files')->delete($link);
        // } else {
        //     app()->make('files')->deleteDirectory($link);
        // }
        // app()->make('files')->delete($link);
        // app()->make('files')->link($target, $link);
    }
}
