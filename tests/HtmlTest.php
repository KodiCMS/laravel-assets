<?php

class HtmlTest extends PHPUnit_Framework_TestCase
{
    public function testScript()
    {
        $script = (new \KodiCMS\Assets\Html())->script('test', ['attr' => 'attr_value']);

        static::assertInstanceOf(\Illuminate\Contracts\Support\Htmlable::class, $script);
        static::assertEquals('<script attr="attr_value" src="http://site.com"></script>', $script->toHtml());
    }

    public function testStyle()
    {
        $script = (new \KodiCMS\Assets\Html())->style('test', ['attr' => 'attr_value']);

        static::assertInstanceOf(\Illuminate\Contracts\Support\Htmlable::class, $script);
        static::assertEquals('<link attr="attr_value" media="all" type="text/css" rel="stylesheet" href="http://site.com">', $script->toHtml());
    }

    public function testAttributes()
    {
        $string = (new \KodiCMS\Assets\Html())->attributes([
            'attr'  => 'attr_value',
            'attr1' => 'attr_value1',
        ]);

        static::assertEquals(' attr="attr_value" attr1="attr_value1"', $string);
    }
}
