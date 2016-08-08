<?php

use Mockery as m;

class TestAssetElement extends \KodiCMS\Assets\AssetElement
{
    /**
     * @return string
     */
    public function render()
    {
        $html = m::mock(\Illuminate\Support\HtmlString::class);


        $html->shouldReceive('toHtml')->once()->andReturn('<test></test>');

        return $html;
    }
}


class CssTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \KodiCMS\Assets\Contracts\AssetElementInterface
     */
    protected $element;

    protected function setUp()
    {
        $this->element = new TestAssetElement('test', 'path/to/src.css', ['test1', 'test2'], ['attr' => 'attr_value']);
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers KodiCMS\Assets\AssetElement::getHandle
     */
    public function testGetHandle()
    {
        static::assertEquals('test', $this->element->getHandle());
    }

    /**
     * @covers KodiCMS\Assets\AssetElement::getSrc
     */
    public function testGetSrc()
    {
        static::assertEquals('path/to/src.css', $this->element->getSrc());
    }

    /**
     * @covers KodiCMS\Assets\AssetElement::getDependency
     */
    public function testGetDependency()
    {
        static::assertTrue(is_array($this->element->getDependency()));
        static::assertContains('test1', $this->element->getDependency());
        static::assertContains('test2', $this->element->getDependency());
        static::assertCount(2, $this->element->getDependency());
    }

    /**
     * @covers KodiCMS\Assets\AssetElement::hasDependency
     */
    public function testHasDependency()
    {
        static::assertTrue($this->element->hasDependency('test1'));
        static::assertTrue($this->element->hasDependency('test2'));
        static::assertFalse($this->element->hasDependency('test3'));
        static::assertTrue($this->element->hasDependency());
    }

    /**
     * @covers KodiCMS\Assets\AssetElement::removeDependency
     */
    public function testRemoveDependency()
    {
        static::assertTrue($this->element->hasDependency('test1'));
        $this->element->removeDependency('test1');
        static::assertFalse($this->element->hasDependency('test1'));

        static::assertTrue($this->element->hasDependency('test2'));
        static::assertCount(1, $this->element->getDependency());
    }

    /**
     * @covers KodiCMS\Assets\AssetElement::getAttributes
     */
    public function testGetAttributes()
    {
        static::assertEquals(['attr' => 'attr_value'], $this->element->getAttributes());
    }

    /**
     * @covers KodiCMS\Assets\AssetElement::render
     * @covers KodiCMS\Assets\AssetElement::__toString
     */
    public function testRender()
    {
        $html = (string) $this->element;
        static::assertEquals('<test></test>', $html);
    }
}
