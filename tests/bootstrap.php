<?php
/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/
require __DIR__.'/../vendor/autoload.php';

$app = new Illuminate\Container\Container();
Illuminate\Container\Container::setInstance($app);

$asset = Mockery::mock('test');
$asset->shouldReceive('asset')->andReturn('http://site.com');

$app->instance('url', $asset);
