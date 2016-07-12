# BearyChat for Laravel

A Laravel integration for the [BearyChat package][1] to send message to the [BearyChat][].

This package is compatible with [Laravel 5](#laravel-5), [Laravel 4](#laravel-4), and [Lumen](#lumen).

+ [Change Log](CHANGELOG.md)
+ :cn: [**中文文档**](README_zh.md)

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

You may access various clients via the `client` method of the `BearyChat` facade, or pass a client name to the `bearychat()` function. The name should correspond to one of the clients listed in your BearyChat configuration file.

```php
BearyChat::client('dev')->send('foo');

bearychat('admin')->send('bar');
```

> **For more advanced usage, please [read the documentation][2] of the BearyChat PHP package.**

### Asynchronous Message

Sending a BearyChat message actually requests the Incoming Webhook via synchronous HTTP, so it will slow down your app execution. For sending asynchronous messages, You can queue them using Laravel's awesome [queue system][].

Here is an example of the Queueable Job for Laravel 5.2:

```php
<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use ElfSundae\BearyChat\Message;
use Exception;
use Log;

class SendBearyChat extends Job implements ShouldQueue
{
    use SerializesModels, InteractsWithQueue;

    /**
     * The Message instance for sending.
     *
     * @var \ElfSundae\BearyChat\Message
     */
    protected $message;

    /**
     * Create a new job instance.
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if ($this->message instanceof Message) {
            try {
                $this->message->send();
            } catch (Exception $e) {
                Log::error(
                    'Exception when processing '.get_class($this)." \n".$e,
                    $this->message->toArray()
                );
                $this->release(10);
            }
        } else {
            $this->delete();
        }
    }
}
```

Then you can dispatch `SendBearyChat` jobs by calling the `dispatch` method on any object which includes the `DispatchesJobs` trait, or just use the `dispatch()` global function:

```php
$order = PayOrder::create($request->all());

dispatch(new \App\Jobs\SendBearyChat(
    bearychat()->text('New order!')
    ->add($order, $order->name, $order->image_url)
));
```

### Sending Laravel Exceptions

A common usage of BearyChat is real-time reporting Laravel exceptions. Just override the `report` method of your exception handler:

```php
/**
 * Report or log an exception.
 *
 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
 *
 * @param  \Exception  $e
 * @return void
 */
public function report(Exception $e)
{
    parent::report($e);

    if (app()->environment('production') &&
        $this->shouldReport($e)) {
        dispatch(new \App\Jobs\SendBearyChat(
            bearychat('server')->text('New Exception!')
            ->notification('New Exception: '.get_class($e))
            ->add([
                'URL' => app('request')->fullUrl(),
                'UserAgent' => app('request')->server('HTTP_USER_AGENT')
            ])
            ->add($e, get_class($e))
        ));
    }
}
```

## License

The BearyChat Laravel package is available under the [MIT license](LICENSE).

[1]: https://github.com/ElfSundae/BearyChat
[2]: https://github.com/ElfSundae/BearyChat/blob/master/README.md
[Webhook]: https://bearychat.com/integrations/incoming
[BearyChat]: https://bearychat.com
[Composer]: https://getcomposer.org
[queue system]: https://laravel.com/docs/5.2/queues
