# Laravel Assets

## Установка

Для установки пакета вы можете выполнить консольную комманду

`composer require kodicms/laravel-assets`

Или добавить пакет в `composer.json`
<pre>
{
  "require": {
    ...
    "kodicms/laravel-assets": "~2.0"
    ...
  }
}
</pre>

###Добавить в загрузку сервис провайдер
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