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
     * @return void
     */
    public function removeDependency($dependency);

    /**
     * @param AssetElementInterface $element
     *
     * @return void
     */
    public function resolveDependency(AssetElementInterface $element);

    /**
     * @return array
     */
    public function getAttributes();

    /**
     * @return string
     */
    public function render();
}
