<?php

namespace Eduka\Services\Listeners\Variants;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Variants\VariantSavedEvent;
use Eduka\Services\Jobs\LemonSqueezy\GetVariantJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;

class VariantSavedListener extends EdukaListener
{
    public function handle(VariantSavedEvent $event)
    {
        $batch = Bus::batch([
            new GetVariantJob($event->variant),
        ])->then(function (Batch $batch) use ($event) {
            // Notify the course admin.
            nova_notify($event->variant->course->admin, [
                'message' => 'LS variant data gathered ('.$event->variant->name.')',
                'icon' => 'dots-circle-horizontal',
                'type' => 'success',
            ]);
        })->catch(function (Batch $batch, Throwable $e) use ($event) {
            // Notify the course admin.
            nova_notify($event->variant->course->admin, [
                'message' => $e->message(),
                'icon' => 'exclamation-circle',
                'type' => 'error',
            ]);
        })->dispatch();
    }
}
