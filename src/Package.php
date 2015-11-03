<?php
namespace KodiCMS\Assets;

use Illuminate\Support\Collection;

class Package extends Collection
{

    /**
     * @var string
     */
    protected $name;


    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }


    /**
     * @return string
     */
    public function getName()
    {
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
        if ( ! isset( $attributes['media'] )) {
            $attributes['media'] = 'all';
        }

        return $this->put(
            $handle . '.css',
            new Css($handle, $src, $dependency, $attributes)
        );
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

        return $this->put(
            $handle . '.js',
            new JavaScript($handle, $src, $dependency, $footer)
        );
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
     * @return static
     */
    public function getJs($includeDependency = false)
    {
        return $this->filter(function ($item) use($includeDependency) {
            $item->includeDependency($includeDependency);

            return $item instanceof JavaScript;
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