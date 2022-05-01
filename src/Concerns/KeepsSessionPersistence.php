<?php

namespace Eduka\Services\Concerns;

use Illuminate\Support\Str;

/**
 * This trait manages a session persistence. What this mean?
 * - Uses the property $this->sessionPrefix, should be instanciated on
 * the class constructor with the prefix to be used.
 * e.g.: $this->sessionPrefix = 'eduka:application:user';.
 */
trait KeepsSessionPersistence
{
    protected $sessionPrefix;

    /**
     * Manages session persistence of a prefix, using the session id.
     *
     * @param  callable  $funct      Callable to execute if session not present.
     * @param  string  $prefix     Session prefix to check.
     * @param  bool  $invalidate Invalidate other session prefixes with
     * different session ids.
     * @return self
     */
    protected function refresh(callable $funct, string $prefix = null, bool $invalidate = true)
    {
        if (! is_null($prefix)) {
            info('assigning a new prefix:' . $prefix);
            $this->sessionPrefix = $prefix;
        } else {
            info('prefix was already assigned:' . $this->sessionPrefix);
        }

        info('session id: ' . session()->getId());

        if ($this->sessionPrefix != null) {
            $canonical = $this->sessionPrefix.':'.session()->getId();
            info('canonical:' . $canonical);

            if ($invalidate) {
                // Remove all session variables that have $this->sessionPrefix.
                collect(session()->all())->each(function ($item, $key) {

                    info('[invalidation] - ' . $key . ' vs ' . $this->sessionPrefix);

                    if (Str::startsWith($key, $this->sessionPrefix)) {
                        info('[forgetting] - Forgetting ' . $key);
                        session()->forget($key);
                    }
                });
            }

            /**
             * If we do have this canonical, let's session it.
             */
            if (session()->has($canonical)) {
                info('-- session was validated, retrieved session -- ');
                $return = session($canonical);
            } else {
                info('-- session NOT validated, computing session -- ');
                /**
                 * If not, we need to execute the callable and
                 * session the value from it (if it's not null).
                 */
                $return = $funct();

                info('-- session computed with return value: ' . $return . ' --');

                if (! is_null($return)) {
                    session([$canonical => $return]);
                    info('session stored:' . $canonical . ' with ' . $return);
                    info(session()->all());
                }
            }
        }

        return $this;
    }

    /**
     * Returns a session prefix value. By default it will return the
     * $this->sessionPrefix attribute, but it can return other values.
     *
     * @param  string $prefix The session prefix.
     *
     * @return mixed|null Anything retrieved from that session prefix
     */
    public function session(string $prefix = null)
    {
        $prefix = $prefix ?? $this->sessionPrefix;

        $canonical = $prefix . ':' . session()->getId();

        return session()->has($canonical) ? session($canonical) : null;
    }
}
