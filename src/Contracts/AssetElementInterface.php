<?php

namespace KodiCMS\Assets\Contracts;

interface AssetElementInterface
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
     * @param string|array $dependency
     *
     * @return array
     */
    public function hasDependency($dependency = null);

    /**
     * @param string $dependency
     *
     * @return array
     */
    public function removeDependency($dependency);

    /**
     * @return string
     */
    public function render();
}
