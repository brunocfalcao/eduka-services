<?php

namespace Eduka\Services\Concerns;

trait CourseFeatures
{
    /**
     * Validation condition to check if the course is decommissioned.
     *
     * @return bool
     */
    public function isDecommissioned()
    {
        return $this->is_decommissioned;
    }

    /**
     * Validation conditions to check if the course is launched. It shouldn't
     * be deleted, not decommissioned and the launch date needs to be
     * in the past.
     *
     * @return bool
     */
    public function isLaunched()
    {
        return blank($this->deleted_at) &&
               ! $this->isDecommissioned() &&
               $this->launched_at <= now() &&
               ! blank($this->launched_at);
    }

    /**
     * Validation conditions to check if the course is in prelaunch.
     * Prelaunch state is when the course is not deleted, is not decommissioned
     * and can be launched, but with a launch date in the future.
     *
     * @return bool
     */
    public function isPrelaunched()
    {
        return blank($this->deleted_at) &&
               ! $this->isDecommissioned() &&
               (blank($this->launched_at) || $this->launched_at > now());
    }

    public function registerSelfProvider()
    {
        app()->register($this->provider_namespace);
    }
}
