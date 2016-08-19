<?php

use Mockery as m;

class MetaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \KodiCMS\Assets\Contracts\MetaInterface
     */
    protected $meta;

    protected function setUp()
    {
        $this->meta = new \KodiCMS\Assets\Meta(
            new \KodiCMS\Assets\Assets(
                new \KodiCMS\Assets\PackageManager()
            )
        );
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \KodiCMS\Assets\Meta::assets
     */
    public function testAssets()
    {
        static::assertInstanceOf(\KodiCMS\Assets\Contracts\AssetsInterface::class, $this->meta->assets());
    }

    /**
     * @covers \KodiCMS\Assets\Meta::setMetaData
     * @covers \KodiCMS\Assets\Meta::setTitle
     * @covers \KodiCMS\Assets\Meta::setMetaDescription
     * @covers \KodiCMS\Assets\Meta::setMetaKeywords
     * @covers \KodiCMS\Assets\Meta::setMetaRobots
     */
    public function testSetMetaData()
    {
        $metaData = m::mock(\KodiCMS\Assets\Contracts\MetaDataInterface::class);

        $metaData->shouldReceive('getMetaTitle')->once()->andReturn('meta_title');
        $metaData->shouldReceive('getMetaDescription')->once()->andReturn('meta_description');
        $metaData->shouldReceive('getMetaKeywords')->once()->andReturn(['tag1', 'tag2']);
        $metaData->shouldReceive('getMetaRobots')->once()->andReturn('meta_robots');

        $this->meta->setMetaData($metaData);

        static::assertEquals('<title>meta_title</title>', $this->meta->getGroup('meta', 'title'));
        static::assertEquals('<meta name="description" content="meta_description" />', $this->meta->getGroup('meta', 'description'));
        static::assertEquals('<meta name="keywords" content="tag1, tag2" />', $this->meta->getGroup('meta', 'keywords'));
        static::assertEquals('<meta name="robots" content="meta_robots" />', $this->meta->getGroup('meta', 'robots'));
    }

    /**
     * @covers \KodiCMS\Assets\Meta::addSocialTags
     */
    public function testAddSocialTags()
    {
        $socialTags = m::mock(\KodiCMS\Assets\Contracts\SocialMediaTagsInterface::class);

        $socialTags->shouldReceive('getOgTitle')->twice()->andReturn('og:title');
        $socialTags->shouldReceive('getOgType')->once()->andReturn('og:type');
        $socialTags->shouldReceive('getOgUrl')->once()->andReturn('og:url');
        $socialTags->shouldReceive('getOgImage')->twice()->andReturn('og:image');
        $socialTags->shouldReceive('getOgDescription')->twice()->andReturn('og:description');

        $this->meta->addSocialTags($socialTags);

        static::assertEquals('<meta property="og:title" content="og:title" name="og:title" />', $this->meta->getGroup('meta', 'og:title'));
        static::assertEquals('<meta property="og:type" content="og:type" name="og:type" />', $this->meta->getGroup('meta', 'og:type'));
        static::assertEquals('<meta property="og:url" content="og:url" name="og:url" />', $this->meta->getGroup('meta', 'og:url'));
        static::assertEquals('<meta property="og:description" content="og:description" name="og:description" />', $this->meta->getGroup('meta', 'og:description'));

        static::assertEquals('<meta itemprop="name" content="og:title" name="google:name" />', $this->meta->getGroup('meta', 'google:name'));
        static::assertEquals('<meta itemprop="description" content="og:description" name="google:description" />', $this->meta->getGroup('meta', 'google:description'));
        static::assertEquals('<meta itemprop="image" content="og:image" name="google:image" />', $this->meta->getGroup('meta', 'google:image'));
    }

    /**
     * @covers \KodiCMS\Assets\Meta::addMeta
     */
    public function testAddMeta()
    {
        $this->meta->addMeta(['param' => 'value', 'arg' => 'value', 'test'], 'test_meta');

        static::assertEquals('<meta param="value" arg="value" test="test" />', $this->meta->getGroup('meta', 'test_meta'));
    }

    /**
     * @covers \KodiCMS\Assets\Meta::setFavicon
     */
    public function testSetFavicon()
    {
        $this->meta->setFavicon('site.com');
        static::assertEquals('<link rel="shortcut icon" href="site.com" type="image/x-icon" />', $this->meta->getGroup('meta', 'favicon'));

        $this->meta->setFavicon('site.com', 'custom rel');
        static::assertEquals('<link rel="custom rel" href="site.com" type="image/x-icon" />', $this->meta->getGroup('meta', 'favicon'));

        $this->meta->setFavicon('site.com', 'custom rel', 'custom type');
        static::assertEquals('<link rel="custom rel" href="site.com" type="custom type" />', $this->meta->getGroup('meta', 'favicon'));
    }
}
