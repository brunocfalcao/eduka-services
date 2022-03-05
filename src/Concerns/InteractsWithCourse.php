<?php

namespace Eduka\Services\Concerns;

use Eduka\Cube\Models\Product;
use Eduka\Cube\Models\User;

trait InteractsWithCourse
{
    public function addProduct(Product $product, bool $notify = false)
    {
        $product->course()->associate($this)->save();
    }

    public function addUser(User $user, bool $asAdmin = false, bool $notify = false)
    {
        $this->users()->syncWithPivotValues(
            [$user->id],
            ['is_admin' => $asAdmin]
        );
    }

    /**
     * Launching a course triggers several actions:
     * 1. Course launched_at = now().
     * 2. Course is_active = true.
     * 3. Email notifications sent to all subscribers (TODO).
     * 4. Email notification sent to all users (TODO).
     *
     * @return void
     */
    public function launch()
    {
        $this->launched_at = now();
        $this->is_active = true;
        $this->update();
    }

    /**
     * Verifies if a course is launched. Meaning, the course is active,
     * has a past launch date.
     *
     * @return bool
     */
    public static function isLaunched()
    {
        return course()->is_active &&
               ! empty(course()->launched_at) &&
               course()->launched_at < now();
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
