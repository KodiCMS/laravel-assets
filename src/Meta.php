<?php

namespace KodiCMS\Assets;

use BadMethodCallException;
use KodiCMS\Assets\Contracts\AssetsInterface;
use KodiCMS\Assets\Contracts\MetaDataInterface;
use KodiCMS\Assets\Contracts\MetaInterface;
use KodiCMS\Assets\Contracts\SocialMediaTagsInterface;

/**
 * @method $this loadPackage(string|array $names)
 * @method $this removeJs(string $handle = null)
 * @method $this addJs(string $handle, string $src, string $dependency = null, bool $footer = false)
 * @method $this addJsElixir(string $filename = 'js/app.js', string $dependency = null, bool $footer = false)
 * @method $this removeCss(string $handle = null)
 * @method $this addCss(string $handle, string $src, string $dependency = null, array $attributes = [])
 * @method $this addCssElixir(string $filename = 'css/all.css', string $dependency = null, array $attributes = [])
 * @method $this getGroup(string $group, string $handle)
 */
class Meta implements MetaInterface
{
    const META_GROUP_NAME = 'meta';

    /**
     * @var AssetsInterface
     */
    protected $assets;

    /**
     * @param AssetsInterface $assets
     */
    public function __construct(AssetsInterface $assets)
    {
        $this->assets = $assets;
    }

    /**
     * @return AssetsInterface
     */
    public function assets()
    {
        return $this->assets;
    }

    /**
     * @param MetaDataInterface $data
     *
     * @return $this
     */
    public function setMetaData(MetaDataInterface $data)
    {
        return $this->setTitle($data->getMetaTitle())
                    ->setMetaDescription($data->getMetaDescription())
                    ->setMetaKeywords($data->getMetaKeywords())
                    ->setMetaRobots($data->getMetaRobots())
                    ->addMeta(['charset' => 'utf-8'], 'meta::charset');
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->addToGroup('title', '<title>:title</title>', [
            ':title' => e($title),
        ]);
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setMetaDescription($description)
    {
        return $this->addMeta(['name' => 'meta_description', 'content' => e($description)]);
    }

    /**
     * @param string|array $keywords
     *
     * @return $this
     */
    public function setMetaKeywords($keywords)
    {
        if (is_array($keywords)) {
            $keywords = implode(', ', $keywords);
        }

        return $this->addMeta(['name' => 'meta_keywords', 'content' => e($keywords)]);
    }

    /**
     * @param string $robots
     *
     * @return $this
     */
    public function setMetaRobots($robots)
    {
        return $this->addMeta(['name' => 'robots', 'content' => e($robots)]);
    }

    /**
     * @param SocialMediaTagsInterface $socialTags
     *
     * @return $this
     */
    public function addSocialTags(SocialMediaTagsInterface $socialTags)
    {
        return $this
            // Open Graph data
            ->addMeta([
                'property' => 'og:title',
                'content'  => $socialTags->getOgTitle(),
                'name'     => 'og:title',
            ])->addMeta([
                'property' => 'og:type',
                'content'  => $socialTags->getOgType(),
                'name'     => 'og:type',
            ])->addMeta([
                'property' => 'og:url',
                'content'  => $socialTags->getOgUrl(),
                'name'     => 'og:url',
            ])->addMeta([
                'property' => 'og:image',
                'content'  => $socialTags->getOgImage(),
                'name'     => 'og:image',
            ])->addMeta([
                'property' => 'og:description',
                'content'  => $socialTags->getOgDescription(),
                'name'     => 'og:description',
            ])

            // Schema.org markup for Google+
            ->addMeta([
                'itemprop' => 'name',
                'content'  => $socialTags->getOgTitle(),
                'name'     => 'google:name',
            ])->addMeta([
                'itemprop' => 'description',
                'content'  => $socialTags->getOgDescription(),
                'name'     => 'google:description',
            ])->addMeta([
                'itemprop' => 'image',
                'content'  => $socialTags->getOgImage(),
                'name'     => 'google:image',
            ]);
    }

    /**
     * @param array       $attributes
     * @param null|string $group
     *
     * @return $this
     */
    public function addMeta(array $attributes, $group = null)
    {
        $meta = '<meta'.(new Html())->attributes($attributes).' />';

        if (is_null($group)) {
            if (isset($attributes['name'])) {
                $group = $attributes['name'];
            } else {
                $group = str_random();
            }
        }

        return $this->addToGroup($group, $meta);
    }

    /**
     * Указание favicon.
     *
     * @param string $url
     * @param string $rel
     *
     * @return $this
     */
    public function setFavicon($url, $rel = 'shortcut icon')
    {
        return $this->addToGroup('icon', '<link rel=":rel" href=":url" type="image/x-icon" />', [
            ':url' => e($url),
            ':rel' => e($rel),
        ]);
    }

    /**
     * @param string      $handle
     * @param string      $content
     * @param array       $params
     * @param null|string $dependency
     *
     * @return $this
     */
    public function addToGroup($handle, $content, $params = [], $dependency = null)
    {
        $this->assets->group(static::META_GROUP_NAME, $handle, strtr($content, $params), $dependency);

        return $this;
    }

    /**
     * @param string|null $handle
     *
     * @return $this
     */
    public function removeFromGroup($handle = null)
    {
        $this->assets->removeGroup(static::META_GROUP_NAME, $handle);

        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->assets->allGroup('meta').PHP_EOL.$this->assets->render();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->render();
    }

    /**
     * @param string $method
     * @param array  $parameters
     *
     * @return $this
     */
    public function __call($method, $parameters)
    {
        if (!method_exists($this->assets, $method)) {
            throw new BadMethodCallException("Method [$method] does not exist.");
        }

        $return = call_user_func_array([$this->assets, $method], $parameters);

        if (strpos(strtolower($method), 'get') === 0) {
            return $return;
        }

        return $this;
    }
}
