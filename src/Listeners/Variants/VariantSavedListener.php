<?php

namespace Eduka\Services\Listeners\Variants;

use Eduka\Abstracts\Classes\EdukaListener;
use Eduka\Cube\Events\Variants\VariantSavedEvent;
use Eduka\Services\Jobs\LemonSqueezy\GetVariantJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Event\Code\Throwable;

class VariantSavedListener extends EdukaListener
{
    public function handle(VariantSavedEvent $event)
    {
        $batch = Bus::batch([
            new GetVariantJob($event->variant),
        ])->then(function (Batch $batch) use ($event) {
            // Notify the course admin.
            nova_notify($event->course->adminUser, [
                'message' => '[ LS ] - Variant info gathered ('.$event->variant->name.')',
                'icon' => 'academic-cap',
                'type' => 'info',
            ]);
        })->catch(function (Batch $batch, Throwable $e) use ($event) {
            // Notify the course admin.
            nova_notify($event->course->adminUser, [
                'message' => $e->message(),
                'icon' => 'exclamation-circle',
                'type' => 'error',
            ]);
        })->dispatch();
    }
}
