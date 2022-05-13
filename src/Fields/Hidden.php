<?php

namespace Pano\Fields;

 class Hidden extends Field
 {
     protected bool $hiddenFromIndex = true;
     protected bool $hiddenFromCreate = true;
 }
