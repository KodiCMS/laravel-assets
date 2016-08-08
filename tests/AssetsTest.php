<?php

use Mockery as m;

class Kernel extends \Illuminate\Foundation\Http\Kernel
{
}

class AssetsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \KodiCMS\Assets\Contracts\AssetsInterface
     */
    protected $assets;

    protected function setUp()
    {
        $this->assets = new \KodiCMS\Assets\Assets(
            new \KodiCMS\Assets\PackageManager()
        );
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \KodiCMS\Assets\Assets::loadPackage
     * @covers \KodiCMS\Assets\Assets::getPackages
     * @covers \KodiCMS\Assets\Assets::removePackages
     */
    public function testLoadPackage()
    {
        $manager = $this->assets->packageManager();

        $package1 = m::mock(\KodiCMS\Assets\Package::class);
        $package1->shouldReceive('getName')->once()->andReturn('test');
        $package1->shouldReceive('hasDependencies')->once()->andReturn(true);
        $package1->shouldReceive('getDependencies')->once()->andReturn(['test2']);

        $package2 = m::mock(\KodiCMS\Assets\Package::class);
        $package2->shouldReceive('getName')->once()->andReturn('test1');
        $package2->shouldReceive('hasDependencies')->once()->andReturn(false);
        $package2->shouldNotReceive('getDependencies');

        $package3 = m::mock(\KodiCMS\Assets\Package::class);
        $package3->shouldReceive('getName')->once()->andReturn('test2');
        $package3->shouldReceive('hasDependencies')->once()->andReturn(false);
        $package3->shouldNotReceive('getDependencies');

        $package4 = m::mock(\KodiCMS\Assets\Package::class);
        $package4->shouldReceive('getName')->once()->andReturn('test3');
        $package4->shouldNotReceive('hasDependencies');
        $package4->shouldNotReceive('getDependencies');

        $manager->add($package1);
        $manager->add($package2);
        $manager->add($package3);
        $manager->add($package4);

        $this->assets->loadPackage('test', 'test1');
        $this->assets->loadPackage('test1', 'test2');

        static::assertCount(3, $this->assets->loadedPackages());
        static::assertEquals(['test', 'test2', 'test1'], $this->assets->loadedPackages());

        $this->assets->removePackages();
        static::assertCount(0, $this->assets->loadedPackages());
    }

    /**
     * @covers \KodiCMS\Assets\Assets::addCss
     * @covers \KodiCMS\Assets\Assets::getCss
     * @covers \KodiCMS\Assets\Assets::getCssList
     * @covers \KodiCMS\Assets\Assets::removeCss
     */
    public function testCss()
    {
        $manager = $this->assets->packageManager();

        $package = m::mock(\KodiCMS\Assets\Package::class);
        $package->shouldReceive('getName')->once()->andReturn('package');
        $package->shouldReceive('hasDependencies')->once()->andReturn(false);

        $package->shouldReceive('getCss')->times(3)->andReturn([
            new \KodiCMS\Assets\Css('package.css', 'path/to/css', [], ['attr' => 'attr_value']),
        ]);

        $manager->add($package);
        $this->assets->loadPackage('package');

        $css = $this->assets->addCss('test', 'path/to/test.css', ['package'], ['attr' => 'attr_value']);
        static::assertInstanceOf(\KodiCMS\Assets\Css::class, $css);

        $this->assets->addCss('test1', 'path/to/test1.css', ['test4', 'test5'], ['attr' => 'attr_value']);

        static::assertEquals(
            '<link attr="attr_value" media="all" type="text/css" rel="stylesheet" href="http://site.com">',
            $this->assets->getCss('test1')
        );

        static::assertEquals(
           '<link attr="attr_value" media="all" type="text/css" rel="stylesheet" href="http://site.com">'.PHP_EOL.
           '<link attr="attr_value" media="all" type="text/css" rel="stylesheet" href="http://site.com">'.PHP_EOL.
           '<link attr="attr_value" media="all" type="text/css" rel="stylesheet" href="http://site.com">',
            $this->assets->getCssList()
        );

        $this->assets->removeCss('test1');
        static::assertEmpty($this->assets->getCss('test1'));

        static::assertEquals(
            '<link attr="attr_value" media="all" type="text/css" rel="stylesheet" href="http://site.com">'.PHP_EOL.
            '<link attr="attr_value" media="all" type="text/css" rel="stylesheet" href="http://site.com">',
            $this->assets->getCssList()
        );

        $this->assets->addCss('test1', 'path/to/test.css', ['test2', 'test3'], ['attr' => 'attr_value']);
        $this->assets->removeCss();

        static::assertEmpty($this->assets->getCss('test'));
        static::assertEmpty($this->assets->getCss('test1'));

        static::assertEquals(
            '<link attr="attr_value" media="all" type="text/css" rel="stylesheet" href="http://site.com">',
            $this->assets->getCssList()
        );
    }

    /**
     * @covers \KodiCMS\Assets\Assets::addJs
     * @covers \KodiCMS\Assets\Assets::getJs
     * @covers \KodiCMS\Assets\Assets::getJsList
     * @covers \KodiCMS\Assets\Assets::removeJs
     */
    public function testJs()
    {
        $manager = $this->assets->packageManager();

        $package = m::mock(\KodiCMS\Assets\Package::class);
        $package->shouldReceive('getName')->once()->andReturn('package');
        $package->shouldReceive('hasDependencies')->once()->andReturn(false);


        $package->shouldReceive('getJs')->twice()->andReturn([
            new \KodiCMS\Assets\Javascript('package.js', 'path/to/js', [], true),
        ]);

        $manager->add($package);
        $this->assets->loadPackage('package');

        $script = $this->assets->addJs('test', 'path/to/test.css', ['test2', 'test3'], true);
        static::assertInstanceOf(\KodiCMS\Assets\Javascript::class, $script);

        $this->assets->addJs('test1', 'path/to/test.css', ['test2', 'test3']);
        $this->assets->addJs('test2', 'path/to/test.css', ['test2', 'test3']);

        static::assertEquals(
            '<script src="http://site.com"></script>',
            $this->assets->getJs('test1')
        );

        static::assertEquals(
            '<script src="http://site.com"></script>'.PHP_EOL.
            '<script src="http://site.com"></script>',
            $this->assets->getJsList()
        );

        static::assertEquals(
            '<script src="http://site.com"></script>'.PHP_EOL.
            '<script src="http://site.com"></script>',
            $this->assets->getJsList(true)
        );

        $this->assets->removeJs('test1');
        static::assertEmpty($this->assets->getJs('test1'));
        static::assertNotEmpty($this->assets->getJs('test2'));

        $this->assets->removeJs(true);

        static::assertEmpty($this->assets->getJs('test'));
        static::assertNotEmpty($this->assets->getJs('test2'));

        $this->assets->removeJs();
        static::assertEmpty($this->assets->getJs('test2'));
    }

    /**
     * @covers \KodiCMS\Assets\Assets::group
     * @covers \KodiCMS\Assets\Assets::getGroup
     * @covers \KodiCMS\Assets\Assets::allGroup
     * @covers \KodiCMS\Assets\Assets::removeGroup
     */
    public function testGroup()
    {
        $this->assets->group('group1', 'title', '<title>hello-world</title>');
        $this->assets->group('group1', 'keywords', '<meta keywords="test" />');
        $this->assets->group('group3', 'keywords', '<meta keywords="test1" />');

        static::assertEquals('<title>hello-world</title>', $this->assets->getGroup('group1', 'title'));
        static::assertEquals('<meta keywords="test" />', $this->assets->getGroup('group1', 'keywords'));
        static::assertEquals('<meta keywords="test1" />', $this->assets->getGroup('group3', 'keywords'));
        static::assertNull($this->assets->getGroup('group2', 'keywords'));

        static::assertEquals(
            '<title>hello-world</title>'.PHP_EOL.
            '<meta keywords="test" />',
            $this->assets->allGroup('group1')
        );

        static::assertEquals(
            '<meta keywords="test1" />',
            $this->assets->allGroup('group3')
        );

        static::assertEquals(
            PHP_EOL,
            $this->assets->allGroup('group2')
        );

        $this->assets->removeGroup('group1', 'title');
        static::assertNull($this->assets->getGroup('group1', 'title'));

        $this->assets->removeGroup('group1');
        static::assertNull($this->assets->getGroup('group1', 'keywords'));
        static::assertNotNull($this->assets->getGroup('group3', 'keywords'));

        $this->assets->removeGroup();
        static::assertNull($this->assets->getGroup('group3', 'keywords'));
    }
}
