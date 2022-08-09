<?php

namespace Pano\Query\Directives;

use Elastico\Query\Compound\Boolean;

 class BooleanDirective extends PatternDirective
 {
     protected Boolean $baseQuery;

     public function __construct(
         public array $directives,
         public null|string $start = '(',
         public null|string $end = ')',
         public bool $allow_single = true, // force delimiters
         public bool $nested = true,
         public null|string $nested_start = '(',
         public null|string $nested_end = ')',
     ) {
         if ($this->nested) {
             $this->nested_start ??= $this->start;
             $this->nested_end ??= $this->end;
         }
     }

     public function handle(Directive $node, null|Directive $parent, mixed $root): void
     {
         // dump(spl_object_id($this).' '.spl_object_id($this->baseQuery).' '.$node->match.' '.spl_object_id($this->getCurrentQuery()));

         if ($node instanceof StartDirective || $node instanceof EndDirective || $node instanceof AndDirective) {
             // Configure default AND | OR
         } elseif ($node instanceof OrDirective) {
             $this->setCurrentQuery(new Boolean());
             $this->baseQuery->should($this->getCurrentQuery());
         } elseif ($node instanceof NotDirective) {
             $negativeQuery = new Boolean();
             $this->getCurrentQuery()->mustNot($negativeQuery);
             $this->setCurrentQuery($negativeQuery);
         } elseif ($node instanceof static) {
         }
     }

     public function getType(): string
     {
         return 'field';
     }

     public function getText()
     {
         return '';
     }

     public function getAlias(): array
     {
         // code...
     }

     public function directives(): array
     {
         $directives = [];

         // dump([static::class.' Generate:', !empty($this->end) && $this->hasStart(), $this]);
         if (collect($this->nodes)->contains(fn ($n) => $n instanceof EndDirective)) {
             return [];
         }

         if (!empty($this->start) && !$this->hasStart() && collect($this->nodes)->filter(fn ($n) => !($n instanceof Whitespace))->isEmpty()) {
             $directives[] = new StartDirective(pattern: '/'.preg_quote($this->start).'/i');
             if (!$this->allow_single) {
                 return $directives;
             }
         }

         if (empty($this->start) || $this->hasStart()) {
             // new NotDirective()
             $directives[] = new AndDirective();
             $directives[] = new OrDirective();
         }
         if (!empty($this->end) && $this->hasStart()) {
             $directives[] = new EndDirective(pattern: '/'.preg_quote($this->end).'/i');
         }
         if (!empty($this->end) && collect($this->nodes)->contains(fn ($n) => !($n instanceof Whitespace)) && !$this->hasStart()) {
             return [];
         }

         if ($this->nested) {
             $clone = clone $this;
             $clone->start = $this->nested_start;
             $clone->end = $this->nested_end;
             $clone->nested = false;
             $clone->allow_single = false;
             $clone->nodes = [];

             // dd($clone->pattern());

             $directives[] = $clone;
         }

         array_push($directives, ...$this->directives);

         return $directives;
     }

     public function hasStart(): bool
     {
         return !empty($this->start) && (str_ends_with($this->match, $this->start) || collect($this->nodes)->contains(fn ($n) => $n instanceof StartDirective));
     }

     public function pattern(): string
     {
         // return '/()/i';
         $regexes = [];
         if ($this->start) {
             $regexes[] = '('.preg_quote($this->start).')';
         }
         if ($this->allow_single) {
             $regexes[] = '()';
         }

         return '/'.implode('|', $regexes).'/i';
     }

     public function suggest($builder): array
     {
         // if (1 == count($this->directives)) {
         //     return $this->directives[0]->suggest($builder);
         // }

         // $rest = rtrim($this->internal_rest, ')');
         $rest = ltrim($this->_debug['internal_rest']);

         return collect($this->directives)
             ->flatMap(fn ($directive) => $directive->complete($builder))
             ->filter(fn ($d) => str_contains($d['text'], $rest))
             ->values()
             ->all()
        ;
     }

     public function initialise(): void
     {
         if (empty($this->baseQuery)) {
             $this->baseQuery = new Boolean();

             $this->getCurrentQuery()->filter($this->baseQuery);
             $this->setCurrentQuery(new Boolean());
             $this->baseQuery->should($this->getCurrentQuery());
         }
     }

     /**
      * The directive doesn't accept new input.
      */
     public function isComplete(): bool
     {
         if (empty($this->end)) {
             return false;
         }

         if ($this->hasStart()) {
             return collect($this->nodes)->contains(fn ($n) => $n instanceof EndDirective);
         }

         return parent::isComplete();
     }
 }
