<?php

use Mockery as m;

class PackageManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \KodiCMS\Assets\Contracts\PackageManagerInterface
     */
    protected $manager;

    protected function setUp()
    {
        $this->manager = new \KodiCMS\Assets\PackageManager();
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \KodiCMS\Assets\PackageManager::add
     */
    public function testAdd()
    {
        $package = $this->manager->add('test');

        static::assertInstanceOf(\KodiCMS\Assets\Contracts\PackageInterface::class, $package);

        $package1 = m::mock(\KodiCMS\Assets\Package::class);
        $package1->shouldReceive('getName')->once()->andReturn('test1');

        $this->manager->add($package1);
        static::assertCount(2, $this->manager);
    }

    public function testLoad()
    {
        $package = $this->manager->add('test');
        static::assertEquals($this->manager->load('test'), $package);
    }
}
