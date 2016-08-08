<?php

use Mockery as m;

class TestJavascriptAssetElement extends \KodiCMS\Assets\Javascript
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

class JavascriptTest extends CssTest
{
    /**
     * @var \KodiCMS\Assets\Javascript
     */
    protected $element;

    protected function setUp()
    {
        $this->element = new TestJavascriptAssetElement('test', 'path/to/src.css', ['test1', 'test2'], 'true', ['attr' => 'attr_value']);
    }

    /**
     * @covers KodiCMS\Assets\AssetElement::isFooter
     * @covers KodiCMS\Assets\AssetElement::setFooter
     */
    public function testSetFooter()
    {
        static::assertTrue($this->element->isFooter());
        $this->element->setFooter(false);
        static::assertFalse($this->element->isFooter());
        $this->element->setFooter(true);
        static::assertTrue($this->element->isFooter());
    }
}
