# !!! This project is not maintained anymore. Please use this package https://github.com/butschster/LaravelMetaTags !!!

### Laravel Assets
[![Build Status](https://travis-ci.org/KodiCMS/laravel-assets.svg?branch=master)](https://travis-ci.org/KodiCMS/laravel-assets)
[![StyleCI](https://styleci.io/repos/45453063/shield)](https://styleci.io/repos/45453063)
## Установка

Для установки пакета вы можете выполнить консольную команду

`composer require kodicms/laravel-assets`

Или добавить пакет в `composer.json`
<pre>
{
  "require": {
    ...
    "kodicms/laravel-assets": "~0.6"
    ...
  }
}
</pre>

### Добавить в загрузку сервис провайдер
<pre>
'providers' => [
  ...
  KodiCMS\Assets\AssetsServiceProvider::class,
  ...
],
'aliases' => [
  ...
  'Assets' => KodiCMS\Assets\Facades\Assets::class,
  'PackageManager' => KodiCMS\Assets\Facades\PackageManager::class,
  'Meta' => KodiCMS\Assets\Facades\Meta::class,
  ...
]
</pre>

## Использование

### Формирование пакетов

```php
PackageManager::add('jquery')
	->js(null, 'https://code.jquery.com/jquery-2.1.4.min.js');

PackageManager::add('jquery-ui')
	->js(null, 'https://code.jquery.com/ui/1.11.4/jquery-ui.min.js', 'jquery')
	->css(null, 'https://code.jquery.com/ui/1.11.4/themes/ui-lightness/jquery-ui.css');

PackageManager::add('custom')
	->js(null, '...')
	->js('custom.second.js', '...', 'custom')
	->css(null, '...')
	->css('custom.second.css', '...');
```

### Добавление данных в вывод

Формировать мета данные для вывода можно не только в шаблоне, но и непосредственно коде приложения

```php
use KodiCMS\Assets\Contracts\SocialMediaTagsInterface;

class Article extends Model implements SocialMediaTagsInterface
{
	...
}

use Meta;

class ArticleController extends Controller
{
	public function show($articleId)
    {
        $article = Article::find($articleId);

        Meta::loadPackage('jquery')
        	->addSocialTags($article);

		Meta::addCss('style', url('css/style.css'));
		Meta::addJs('scripts', url('js/scripts.js'), 'jquery');

		Meta::addJsElixir();
		...
    }
}
```


### Вывод
Для вывода css и js в шаблон сайта используется класс Meta.

```html
<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>
	{!!
		Meta::setFavicon('favicon.ico')
			// Подключение файлов из пакетов
			->loadPackage('jquery', 'jquery-ui', 'custom')

			// Meta title
			->setTitle('Hello world')
		    ->setMetaDescription('Meta description')
		    ->setMetaKeywords('Meta keywords')
		    ->setMetaRobots('Meta robots')

		    // Alternative meta title
			->setMetaData(MetaDataInterface $data)

			// Social tags
		    ->addSocialTags(SocialMediaTagsInterface $socialTags)

		    // Custom tag
		    ->addMeta([
				'property' => 'og:title',
				'content'  => 'Title',
				'name' => 'og:title'
			])

			->render()
	!!}
</head>
```

## Вывод списка пакетов

`php artisan assets:packages`
