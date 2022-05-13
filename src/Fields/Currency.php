<?php

namespace Pano\Fields;

 /**
  * Use brick/money package.
  */
 class Currency extends Number
 {
     protected string $currency = 'CHF';
     protected string $currency_locale = 'en';

     public function currency(string $currency): static
     {
         $this->currency = $currency;

         return $this;
     }

     public function locale(string $locale): static
     {
         $this->currency_locale = $locale;

         return $this;
     }
 }
