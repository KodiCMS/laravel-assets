<?php

use Mockery as m;

class PackageTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \KodiCMS\Assets\Package::create
     */
    public function testCreate()
    {
        $package = \KodiCMS\Assets\Package::create('test');

        static::assertInstanceOf(\KodiCMS\Assets\Contracts\PackageInterface::class, $package);
        static::assertEquals('test', $package->getName());
    }

    /**
     * @covers \KodiCMS\Assets\Package::with
     * @covers \KodiCMS\Assets\Package::hasDependencies
     * @covers \KodiCMS\Assets\Package::getDependencies
     * @covers \KodiCMS\Assets\Package::addDependency
     */
    public function tesDependencies()
    {
        $package = \KodiCMS\Assets\Package::create('test');

        static::assertFalse($package->hasDependencies());
        static::assertEquals([], $package->getDependencies());
        $package->with('test1', 'test2');

        static::assertEquals(['test1', 'test2'], $package->getDependencies());
        static::assertTrue($package->hasDependencies());

        $package->addDependency('test3', 'test4');

        static::assertEquals(['test1', 'test2', 'test3', 'test4'], $package->getDependencies());
    }

    /**
     * \KodiCMS\Assets\Package::setName
     * \KodiCMS\Assets\Package::getName.
     */
    public function testSetName()
    {
        $package = new \KodiCMS\Assets\Package();

        $package->setName('test');
        static::assertEquals('test', $package->getName());
    }

    /**
     * @covers \KodiCMS\Assets\Package::getName
     * @expectedException \KodiCMS\Assets\Exceptions\PackageException
     */
    public function testGetNameException()
    {
        $package = new \KodiCMS\Assets\Package();

        $package->getName();
    }

    /**
     * @covers\KodiCMS\Assets\Package::js
     * @covers\KodiCMS\Assets\Package::getJs
     * @covers\KodiCMS\Assets\Package::render
     */
    public function testScripts()
    {
        $package = new \KodiCMS\Assets\Package();

        $package->js('test', 'path/to/js');
        $package->js('test', 'path/to/js2');
        $package->js('test1', 'path/to/js1');

        $scripts = $package->getJs();

        static::assertCount(2, $scripts);
        static::assertEquals(
            '<script src="http://site.com"></script>'.PHP_EOL.
            '<script src="http://site.com"></script>',
            $scripts->render()
        );
    }

    /**
     * @covers\KodiCMS\Assets\Package::css
     * @covers\KodiCMS\Assets\Package::getCss
     * @covers\KodiCMS\Assets\Package::render
     */
    public function testStyles()
    {
        $package = new \KodiCMS\Assets\Package();

        $package->css('test', 'path/to/css');
        $package->css('test', 'path/to/css2');
        $package->css('test1', 'path/to/css1');

        $styles = $package->getCss();

        static::assertCount(2, $styles);
        static::assertEquals(
            '<link media="all" type="text/css" rel="stylesheet" href="http://site.com">'.PHP_EOL.
            '<link media="all" type="text/css" rel="stylesheet" href="http://site.com">',
            $styles->render()
        );
    }
}
