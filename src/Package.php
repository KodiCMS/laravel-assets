<?php

namespace KodiCMS\Assets;

use Illuminate\Support\Collection;
use KodiCMS\Assets\Exceptions\PackageException;

class Package extends Collection
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $dependency = [];

    /**
     * @param array|string $packages
     *
     * @return $this
     */
    public function with($packages)
    {
        $packages = is_array($packages) ? $packages : func_get_args();

        $this->dependency = $packages;

        return $this;
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return array_unique($this->dependency);
    }

    /**
     * @return bool
     */
    public function hasDependencies()
    {
        return count($this->dependency) > 0;
    }

    /**
     * @param array|string $packages
     *
     * @return $this
     */
    public function addDependency($packages)
    {
        $packages = is_array($packages) ? $package : func_get_args();

        foreach ($packages as $package) {
            $this->dependency[] = $package;
        }

        return $this;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @throws \Exception
     *
     * @return string
     */
    public function getName()
    {
        if (empty($this->name)) {
            throw new PackageException('Package name not set');
        }

        return $this->name;
    }

    /**
     * @param string       $handle
     * @param string       $src
     * @param array|string $dependency
     * @param array        $attributes
     *
     * @return $this
     */
    public function css($handle = null, $src = null, $dependency = null, array $attributes = [])
    {
        if ($handle === null) {
            $handle = $this->getName();
        }

        // Set default media attribute
        if (!isset($attributes['media'])) {
            $attributes['media'] = 'all';
        }

        return $this->put($handle.'.css', new Css($handle, $src, $dependency, $attributes));
    }

    /**
     * @param string|bool $handle
     * @param string      $src
     * @param array       $dependency
     * @param bool        $footer
     *
     * @return $this
     */
    public function js($handle = false, $src = null, $dependency = null, $footer = false)
    {
        if ($handle === null) {
            $handle = $this->getName();
        }

        return $this->put($handle.'.js', new Javascript($handle, $src, $dependency, $footer));
    }

    /**
     * @return static
     */
    public function getCss()
    {
        return $this->filter(function ($item) {
            return $item instanceof Css;
        });
    }

    /**
     * @param bool $includeDependency
     *
     * @return static
     */
    public function getJs($includeDependency = false)
    {
        return $this->filter(function ($item) use ($includeDependency) {
            return $item instanceof Javascript;
        });
    }

    /**
     * @return string
     */
    public function render()
    {
        $string = '';

        foreach ($this as $asset) {
            $string .= $asset->render();
        }

        return $string;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
