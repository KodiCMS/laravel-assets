<?php

namespace KodiCMS\Assets;

use KodiCMS\Assets\Contracts\MetaDataInterface;
use KodiCMS\Assets\Contracts\SocialMediaTagsInterface;

class Meta
{
    const META_GROUP_NAME = 'meta';

    /**
     * Конструктор.
     *
     * При передачи объекта страницы в нем генерируется
     *
     *        <title>...</title>
     *        <meta name="keywords" content="" />
     *        <meta name="description" content="" />
     *        <meta name="robots" content="" />
     *        <meta name="robots" content="" />
     *        <meta charset="utf-8">
     *
     * @param MetaDataInterface $data
     */
    public function __construct(MetaDataInterface $data = null)
    {
        if (!is_null($data)) {
            $this->setMetaData($data);
        }
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
     * @return mixed
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
     * @return Meta
     */
    public function setMetaDescription($description)
    {
        return $this->addMeta(['name' => 'meta_description', 'content' => e($description)]);
    }

    /**
     * @param string|array $keywords
     *
     * @return Meta
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
     * @return Meta
     */
    public function setMetaRobots($robots)
    {
        return $this->addMeta(['name' => 'robots', 'content' => e($robots)]);
    }

    /**
     * @param SocialMediaTagsInterface $socialTags
     *
     * @return Meta
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
        $meta = '<meta'.app('html')->attributes($attributes).' />';

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
     * @param string      $filename   [default: css/all.css]
     * @param null|string $dependency
     * @param array|null  $attributes
     *
     * @return $this
     */
    public function addCssElixir($filename = 'css/all.css', $dependency = null, array $attributes = [])
    {
        return $this->addCss('elixir.css', elixir($filename), $dependency, $attributes);
    }

    /**
     * @param string      $handle
     * @param string      $src
     * @param null|string $dependency
     * @param null|array  $attributes
     *
     * @return $this
     */
    public function addCss($handle, $src, $dependency = null, array $attributes = [])
    {
        app('assets')->addCss($handle, $src, $dependency, $attributes);

        return $this;
    }

    /**
     * @param null|string $handle
     *
     * @return $this
     */
    public function removeCss($handle = null)
    {
        app('assets')->removeCss($handle);

        return $this;
    }

    /**
     * @param string      $filename   [default: js/app.js]
     * @param null|string $dependency
     * @param bool        $footer
     *
     * @return $this
     */
    public function addJsElixir($filename = 'js/app.js', $dependency = null, $footer = false)
    {
        return $this->AddJs('elixir.js', elixir($filename), $dependency, $footer);
    }

    /**
     * @param string      $handle
     * @param string      $src
     * @param null|string $dependency
     * @param bool        $footer
     *
     * @return $this
     */
    public function AddJs($handle, $src, $dependency = null, $footer = false)
    {
        app('assets')->addJs($handle, $src, $dependency, $footer);

        return $this;
    }

    /**
     * @param null|string $handle
     *
     * @return $this
     */
    public function removeJs($handle = null)
    {
        app('assets')->removeJs($handle);

        return $this;
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
     * @param string|array $names
     *
     * @return $this
     */
    public function loadPackage($names)
    {
        $names = is_array($names) ? $names : func_get_args();

        app('assets')->loadPackage($names);

        return $this;
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
        app('assets')->group(static::META_GROUP_NAME, $handle, strtr($content, $params), $dependency);

        return $this;
    }

    /**
     * @param string|null $handle
     *
     * @return $this
     */
    public function removeFromGroup($handle = null)
    {
        app('assets')->removeGroup(static::META_GROUP_NAME, $handle);

        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        return app('assets')->allGroup('meta').PHP_EOL.app('assets')->render();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->build();
    }
}
