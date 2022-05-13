<?php

namespace Pano\Fields;

use Pano\Metrics\Trend;

 /**
  * Show small graph on index or detail pages.
  */
 class Sparkline extends Field
 {
     public array $data;
     public int $height;
     public int $width;
     public bool $as_bar_chart;

     public function data(array|callable|Trend $data): static
     {
         $this->data = is_callable($data) ? $data() : $data;

         return $this;
     }

     public function asBarChart(): static
     {
         $this->as_bar_chart = true;

         return $this;
     }

     public function height(int $height)
     {
         $this->height = $height;

         return $this;
     }

     public function width(int $width)
     {
         $this->width = $width;

         return $this;
     }
 }
