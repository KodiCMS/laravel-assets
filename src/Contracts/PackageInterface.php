<?php

namespace KodiCMS\Assets\Contracts;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use KodiCMS\Assets\Exceptions\PackageException;

interface PackageInterface extends Renderable
{
    /**
     * @param array|string $packages
     *
     * @return $this
     */
    public function with($packages);

    /**
     * @return array
     */
    public function getDependencies();

    /**
     * @return bool
     */
    public function hasDependencies();

    /**
     * @param array|string $packages
     *
     * @return $this
     */
    public function addDependency($packages);

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @throws PackageException
     *
     * @return string
     */
    public function getName();

    /**
     * @param string       $handle
     * @param string       $src
     * @param array|string $dependency
     * @param array        $attributes
     *
     * @return $this
     */
    public function css($handle = null, $src = null, $dependency = null, array $attributes = []);

    /**
     * @param string|bool $handle
     * @param string      $src
     * @param array       $dependency
     * @param bool        $footer
     *
     * @return $this
     */
    public function js($handle = false, $src = null, $dependency = null, $footer = false);

    /**
     * @return AssetElementInterface[]|Collection
     */
    public function getCss();

    /**
     * @param bool $includeDependency
     *
     * @return AssetElementInterface[]|Collection
     */
    public function getJs($includeDependency = false);
}
