<?php

namespace Pano\Fields;

 class File extends Field
 {
     protected string $disk;

     protected bool $deletable = true; // file can be deleted
     protected bool $prunable = false; // delete file when model is deleted

     protected bool $allowsDownload = true;

     protected string $storeOriginalName;
     protected string $storeSize;

     public function disk(string $disk): static
     {
         $this->disk = $disk;

         return $this;
     }

     /**
      * .dmg , .dmg,.exe,.deb , image/*, audio/* ,video/*
      *  http://www.iana.org/assignments/media-types/.
      */
     public function acceptedTypes(string $acceptedTypes): static
     {
         $this->acceptedTypes = $acceptedTypes;

         return $this;
     }

     public function disableDownload(): static
     {
         $this->allowsDownload = false;

         return $this;
     }

     public function store(callable $callable): static
     {
         $this->store = $callable;

         $callable = function ($request) {
             $path = $request->attachment->store('/', 'public');

             // $user->file->update(['path' => $path]);
             // Fields added to the model
             return [
                 'attachment' => $path,
                 'attachment_name' => $request->attachment->getClientOriginalName(),
                 'attachment_size' => $request->attachment->getSize(),
             ];
         };

         return $this;
     }

     public function delete(callable $callable): static
     {
         // same as with store but for delete
     }

     public function preview(callable $callable): static
     {
         $this->preview = $callable;

         //$callable = fn ($value, $disk) => $value ? Storage::disk($disk)->url($value) : null;

         return $this;
     }

     public function thumbnail(callable $callable): static
     {
         $this->thumbnail = $callable;

         return $this;
     }

     public function download(callable $callable): static
     {
         $this->download = $callable;

         return $this;
     }

     /**
      * Name given to the file.
      */
     public function storeAs(string $storeAs): static
     {
         $this->storeAs = $storeAs;

         return $this;
     }

     public function storeOriginalName(string $column): static
     {
         $this->storeOriginalName = $column;

         return $this;
     }

     public function storeSize(string $column): static
     {
         $this->storeSize = $column;

         return $this;
     }

     public function deletable(bool $deletable = true): static
     {
         $this->deletable = $deletable;

         return $this;
     }

     public function prunable(bool $prunable = true): static
     {
         $this->prunable = $prunable;

         return $this;
     }
 }
