<?php

namespace KodiCMS\Assets\Contracts;

use KodiCMS\Assets\Package;

interface PackageManagerInterface
{
    /**
     * @param string|Package $package
     *
     * @return Package
     */
    public function add($package);

    /**
     * @param string $name
     *
     * @return Package|null
     */
    public function load($name);

    /**
     * @param array|string $names
     *
     * @return array
     */
    public function getScripts($names);

    /**
     * @return array
     */
    public function getHTMLSelectChoice();
}
