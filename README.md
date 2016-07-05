# BearyChat for Laravel

A Laravel integration for the [BearyChat package][1] to send message to the [BearyChat][].

This package is compatible with [Laravel 5](#laravel-5), [Laravel 4](#laravel-4), and [Lumen](#lumen).

## Installation

You can install this package using the [Composer][] manager:
```
composer require elfsundae/bearychat-laravel
```
After updating composer, you may configure your app according to the following steps:

### Laravel 5

Add the service provider to the `providers` array in `config/app.php`:
```php
ElfSundae\BearyChat\Laravel\ServiceProvider::class,
```
Then publish the config file:
```shell
$ php artisan vendor:publish --provider="ElfSundae\BearyChat\Laravel\ServiceProvider"
```
Next, configure your BearyChat clients by editing the config file in `config/bearychat.php`.

### Laravel 4

Add the service provider to the `providers` array in `config/app.php`:
```php
'ElfSundae\BearyChat\Laravel\ServiceProvider',
```
Then publish the config file:
```shell
$ php artisan config:publish elfsundae/bearychat-laravel
```
Next, configure your BearyChat clients by editing the config file in `app/config/packages/elfsundae/bearychat-laravel/config.php`.

### Lumen

Register the service provider in `bootstrap/app.php`:
```php
$app->register(ElfSundae\BearyChat\Laravel\ServiceProvider::class);
```
Then copy the config file from this package to your app's `config/bearychat.php`:
```shell
$ cp vendor/elfsundae/bearychat-laravel/src/config/config.php config/bearychat.php
```
Next, you should enable this config file in `bootstrap/app.php`:
```php
$app->configure('bearychat');
```
If you would like to use the `BearyChat` facade, you should uncomment the `$app->withFacades()` call in your `bootstrap/app.php` file.

## License

The BearyChat PHP package is available under the [MIT license](LICENSE).

[1]: https://github.com/ElfSundae/BearyChat
[Webhook]: https://bearychat.com/integrations/incoming
[BearyChat]: https://bearychat.com
[Composer]: https://getcomposer.org
