<?php

namespace Pano\Controllers;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    /**
     * Handle a Request for a Page Component.
     */
    public function __invoke()
    {
        // if (request()->wantsJson()) {
        //     $page = $this->resolvePage();
        //     $endpoints = $page->getEndpoints();

        //     return $endpoint->handle(request());
        // }

        return view('Pano::pano');
    }
}
