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
            $package = Package::create($name);
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
}
