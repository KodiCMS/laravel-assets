<?php

namespace KodiCMS\Assets\Traits;

use KodiCMS\Assets\Contracts\PackageInterface;

trait Packages
{
    use Styles, Scripts;

    /**
     * @var PackageInterface[]
     */
    protected $packages = [];

    /**
     * @var bool
     */
    protected $includeDependency = false;

    /**
     * @param string|array $names
     *
     * @return $this
     */
    public function loadPackage($names)
    {
        $names = is_array($names) ? $names : func_get_args();

        foreach ($names as $name) {
            if (!array_key_exists($name, $this->packages)) {

                /** @var PackageInterface $package */
                if (!is_null($package = $this->manager->load($name))) {
                    $this->packages[$name] = $package;

                    if ($package->hasDependencies()) {
                        foreach ($package->getDependencies() as $dependency) {
                            $this->loadPackage($dependency);
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function loadedPackages()
    {
        return array_keys($this->packages);
    }

    /**
     * @return $this
     */
    public function removePackages()
    {
        $this->packages = [];

        return $this;
    }

    protected function loadPackageCss()
    {
        foreach ($this->packages as $package) {
            foreach ($package->getCss() as $css) {
                $this->styles[$css->getHandle()] = $css;
            }
        }
    }

    protected function loadPackageJs()
    {
        foreach ($this->packages as $package) {
            foreach ($package->getJs($this->includeDependency) as $javaScript) {
                $this->scripts[$javaScript->getHandle()] = $javaScript;
            }
        }
    }
}
