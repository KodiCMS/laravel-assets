<?php

namespace KodiCMS\Assets;

use Illuminate\Support\Collection;
use KodiCMS\Assets\Contracts\PackageInterface;
use KodiCMS\Assets\Contracts\PackageManagerInterface;

class PackageManager extends Collection implements PackageManagerInterface
{
    /**
     * @param string|PackageInterface $package
     *
     * @return Package
     */
    public function add($package)
    {
        if ((!$package instanceof PackageInterface)) {
            $name = $package;
            $package = new Package($name);
        }

        $this->put($package->getName(), $package);

        return $package;
    }

    /**
     * @param string $name
     *
     * @return PackageInterface|null
     */
    public function load($name)
    {
        return $this->get($name);
    }

    /**
     * @param array|string $names
     *
     * @return array
     */
    public function getScripts($names)
    {
        if (!is_array($names)) {
            $names = [$names];
        }

        $scripts = [];

        foreach ($names as $name) {

            /** @var PackageInterface $package */
            $package = $this->load($name);

            if (is_null($package)) {
                continue;
            }

            $scripts += $package->getJs()->all();
        }

        return $scripts;
    }

    /**
     * @return array
     */
    public function getHTMLSelectChoice()
    {
        $options = $this->keys()->all();

        return array_combine($options, $options);
    }
}
