<?php

namespace Eduka\Services\Concerns;

use Eduka\Cube\Models\Product;
use Eduka\Cube\Models\User;

trait InteractsWithCourse
{
    public function withProduct(string $type)
    {
        return $this->products()->firstWhere('type', $type);
    }

    public function addProduct(Product $product, bool $notify = false)
    {
        $product->course()->associate($this)->save();
    }

    public function addUser(User $user, bool $asAdmin = false, bool $notify = false)
    {
        $this->users()->attach([
            $user->id => ['is_admin' => $asAdmin],
        ]);
    }

    /**
     * Register the course service provider.
     *
     * @return void
     */
    public function registerProvider()
    {
        app()->register($this->provider_namespace);
    }
}
