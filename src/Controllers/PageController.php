<?php

namespace Pano\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Arrayable;
use Pano\Facades\Pano;

class PageController extends Controller
{
    /**
     * Handle a Request for a Page Component.
     */
    public function __invoke()
    {
        if (request()->wantsJson()) {
            $page = Pano::context(request()->route()->getName());
            $component = request()->input('uiPath') ? $page->context(request()->input('uiPath')) : $page;
            $endpoint = request()->input('endpoint');
            // response(get_class($component))->send();

            return collect($component->data())
                ->filter(fn ($data, $key) => $endpoint == $key)
                ->map(fn ($data, $key) => is_callable($data) ? $data(request()) : $data)
                ->each(fn (Arrayable|array $item) => $item)
                ->toArray()
            ;
        }

        return view('Pano::pano');
    }
}
