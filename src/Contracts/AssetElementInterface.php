<?php

namespace KodiCMS\Assets\Contracts;

use Illuminate\Contracts\Support\Renderable;

interface AssetElementInterface extends Renderable
{
    /**
     * @return string
     */
    public function getHandle();

    /**
     * @return string
     */
    public function getSrc();

    /**
     * @return array
     */
    public function getDependency();

    /**
     * @param string|null $dependency
     *
     * @return bool
     */
    public function hasDependency($dependency = null);

    /**
     * @param string $dependency
     *
     * @return array
     */
    public function removeDependency($dependency);

    /**
     * @return array
     */
    public function getAttributes();

    /**
     * @return string
     */
    public function render();
}
