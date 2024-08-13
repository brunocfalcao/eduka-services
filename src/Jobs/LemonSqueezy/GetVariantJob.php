<?php

namespace Eduka\Services\Jobs\LemonSqueezy;

use Eduka\Cube\Models\Variant;
use Eduka\Payments\PaymentProviders\LemonSqueezy\LemonSqueezy;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class GetVariantJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $variant;

    public function __construct(Variant $variant)
    {
        $this->variant = $variant;
    }

    public function handle()
    {
        $api = new LemonSqueezy(
            $this->variant->course->lemon_squeezy_api_key
        );

        $data = $api->getVariant($this->variant->product_id);

        $this->variant->withoutEvents(function () use ($data) {
            $this->variant->update(['lemon_squeezy_data' => Arr::dot(json_decode($data, true))]);
        });
    }
}
