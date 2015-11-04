<?php

namespace KodiCMS\Assets;

use Illuminate\Support\Collection;

class PackageManager extends Collection
{
    /**
     * @param string|Package $package
     *
     * @return Package
     */
    public function add($package)
    {
        if ((!$package instanceof Package)) {
            $name = $package;
            $package = new Package();
            $package->setName($name);
        }

        $this->put($package->getName(), $package);

        return $package;
    }

    /**
     * @param string $name
     *
     * @return Package|null
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

            /** @var Package $package */
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
