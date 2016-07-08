# BearyChat for Laravel

A Laravel integration for the [BearyChat package][1] to send message to the [BearyChat][].

This package is compatible with [Laravel 5](#laravel-5), [Laravel 4](#laravel-4), and [Lumen](#lumen).

## Installation

You can install this package using the [Composer][] manager:
```
composer require elfsundae/laravel-bearychat
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
$ php artisan config:publish elfsundae/laravel-bearychat
```
Next, configure your BearyChat clients by editing the config file in `app/config/packages/elfsundae/laravel-bearychat/config.php`.

### Lumen

Register the service provider in `bootstrap/app.php`:
```php
$app->register(ElfSundae\BearyChat\Laravel\ServiceProvider::class);
```
Then copy the config file from this package to your app's `config/bearychat.php`:
```shell
$ cp vendor/elfsundae/laravel-bearychat/src/config/config.php config/bearychat.php
```
Next, you should enable this config file in `bootstrap/app.php`:
```php
$app->configure('bearychat');
```
Now you can configure your BearyChat clients by editing `config/bearychat.php`.

If you would like to use the `BearyChat` facade, you should uncomment the `$app->withFacades()` call in your `bootstrap/app.php` file.

## Usage

You can obtain the BearyChat `Client` using the `BearyChat` facade, or the `bearychat()` helper function. 

```php
BearyChat::send('message');

bearychat()->sendTo('@elf', 'Hi!');
```

You may access various clients via the `client` method of the `BearyChat` facade, or pass a client name to the `bearychat()` function. The name should correspond to one of the clients listed in your bearychat configuration file. By default, a client named "default" will be used.

```php
BearyChat::client('dev')->send('foo');

bearychat('admin')->send('bar');
```

> **For more advanced usage, please [read the documentation][2] of the BearyChat PHP package.**

## License

The BearyChat Laravel package is available under the [MIT license](LICENSE).

[1]: https://github.com/ElfSundae/BearyChat
[2]: https://github.com/ElfSundae/BearyChat/blob/master/README.md
[Webhook]: https://bearychat.com/integrations/incoming
[BearyChat]: https://bearychat.com
[Composer]: https://getcomposer.org
