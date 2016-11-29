# BearyChat for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elfsundae/laravel-bearychat.svg?style=flat-square)](https://packagist.org/packages/elfsundae/laravel-bearychat)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/ElfSundae/Laravel-BearyChat/master.svg?style=flat-square)](https://travis-ci.org/ElfSundae/Laravel-BearyChat)
[![StyleCI](https://styleci.io/repos/62485352/shield)](https://styleci.io/repos/62485352)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/e735bfe1-aa58-49d9-b380-1023fb0e93bf.svg?style=flat-square)](https://insight.sensiolabs.com/projects/e735bfe1-aa58-49d9-b380-1023fb0e93bf)
[![Quality Score](https://img.shields.io/scrutinizer/g/ElfSundae/Laravel-BearyChat.svg?style=flat-square)](https://scrutinizer-ci.com/g/ElfSundae/Laravel-BearyChat)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/ElfSundae/Laravel-BearyChat/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/ElfSundae/Laravel-BearyChat/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/elfsundae/laravel-bearychat.svg?style=flat-square)](https://packagist.org/packages/elfsundae/laravel-bearychat)

这个 Laravel 扩展包封装了 [BearyChat PHP 扩展包][1]，用于向 [BearyChat][] 发送 [机器人消息][Webhook]。

该扩展包兼容 [Laravel 5](#laravel-5) 、 [Laravel 4](#laravel-4) 和 [Lumen](#lumen)。

> - :us: [**Documentation in English**](README.md)
> - **Laravel Notification Channel:** [BearyChatChannel][]

## 目录

<!-- MarkdownTOC -->

- [安装](#安装)
    - [Laravel 5](#laravel-5)
    - [Laravel 4](#laravel-4)
    - [Lumen](#lumen)
- [使用方法](#使用方法)
    - [基础用法](#基础用法)
    - [异步消息](#异步消息)
    - [报告 Laravel 异常](#报告-laravel-异常)
    - [响应 Outgoing](#响应-outgoing)
    - [自定义 Guzzle](#自定义-guzzle)
- [更新日志](#更新日志)
- [测试](#测试)
- [许可协议](#许可协议)

<!-- /MarkdownTOC -->

## 安装

你可以使用 [Composer][] 安装此扩展包：

```sh
$ composer require elfsundae/laravel-bearychat
```

更新完 composer 后，你可以根据以下指引来配置你的 Laravel 应用。

### Laravel 5

将 service provider 添加到 `config/app.php` 中的 `providers` 数组中。

```php
ElfSundae\BearyChat\Laravel\ServiceProvider::class,
```

然后发布 BearyChat 的配置文件：

```sh
$ php artisan vendor:publish --provider="ElfSundae\BearyChat\Laravel\ServiceProvider"
```

编辑配置文件 `config/bearychat.php` ，配置 webhook 和消息预设值。

### Laravel 4

将 service provider 添加到 `config/app.php` 中的 `providers` 数组中。

```php
'ElfSundae\BearyChat\Laravel\ServiceProvider',
```

然后发布 BearyChat 的配置文件：

```sh
$ php artisan config:publish elfsundae/laravel-bearychat
```

编辑配置文件 `app/config/packages/elfsundae/laravel-bearychat/config.php` ，配置 webhook 和消息预设值。

### Lumen

在 `bootstrap/app.php` 中注册 service provider:

```php
$app->register(ElfSundae\BearyChat\Laravel\ServiceProvider::class);
```

然后从扩展包目录拷贝 BearyChat 配置文件到你应用的 `config/bearychat.php`:

```sh
$ cp vendor/elfsundae/laravel-bearychat/src/config/config.php config/bearychat.php
```

为了使配置生效，必须在 `bootstrap/app.php` 中激活：

```php
$app->configure('bearychat');
```

编辑配置文件 `config/bearychat.php` ，配置 webhook 和消息预设值。

如果你想使用 `BearyChat` 门面 (facade)，必须在 `bootstrap/app.php` 文件中取消 `$app->withFacades()` 的代码注释。

## 使用方法

### 基础用法

通过 `BearyChat` 门面 (facade) 或者 `bearychat()` 帮助函数，可以得到 BearyChat `Client` 实例。

```php
BearyChat::send('message');

bearychat()->sendTo('@elf', 'Hi!');
```

调用 `BearyChat` 门面的 `client` 方法并传入一个 client 名字，或者将 client 名字传入 `bearychat()` 函数，可以得到其他不同的 BearyChat `Client` 实例。作为参数的 client 名字必须在 BearyChat 的配置文件中事先定义。

```php
BearyChat::client('dev')->send('foo');

bearychat('admin')->send('bar');
```

> **更多高级用法，请参阅 [BearyChat PHP 扩展包的文档][2]。**

### 异步消息

发送一条 BearyChat 消息实际上是向 Incoming Webhook 发送同步 HTTP 请求，所以这在一定程度上会延长应用的响应时间。可以使用 Laravel 强悍的[队列系统][queue system]来异步发送消息。

下面是一个 Laravel 5.3 应用的队列任务的示例：

```php
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use ElfSundae\BearyChat\Message;

class SendBearyChat implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The BearyChat client.
     *
     * @var \ElfSundae\BearyChat\Client
     */
    protected $client;

    /**
     * The Message instance to be sent.
     *
     * @var \ElfSundae\BearyChat\Message
     */
    protected $message;

    /**
     * Create a new job instance.
     *
     * @param  mixed  $message  A Message instance, or parameters which can be handled
     *                          by the `send` method of a Message instance.
     */
    public function __construct($message = null)
    {
        if ($message instanceof Message) {
            $this->message = $message;
        } elseif (is_string($message)) {
            $this->text($message);

            if (func_num_args() > 1) {
                if (is_bool($markdown = func_get_arg(1))) {
                    $this->markdown(func_get_arg(1));

                    if (func_num_args() > 2) {
                        $this->notification(func_get_arg(2));
                    }
                } else {
                    call_user_func_array([$this, 'add'], array_slice(func_get_args(), 1));
                }
            }
        }
    }

    /**
     * Any unhandled methods will be sent to the Message instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        $message = $this->message ?: new Message($this->client ?: bearychat());

        $this->message = call_user_func_array([$message, $method], $parameters);

        return $this;
    }

    /**
     * Set the client with client name.
     *
     * @param  string  $name
     * @return $this
     */
    public function client($name)
    {
        $this->client = bearychat($name);

        return $this;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if ($this->client) {
            $this->client->sendMessage($this->message);
        } else {
            $this->message->send();
        }
    }
}
```

然后在任意包含了 `DispatchesJobs` trait 的类中调用 `dispatch` 方法，或者使用全局的 `dispatch()` 函数，就可以将 `SendBearyChat` 任务派遣到队列中执行。例如：

```php
dispatch(new SendBearyChat('hello'));

dispatch(new SendBearyChat('hello', true, 'notification'));

dispatch(new SendBearyChat('hello', 'attachment content', 'attachment title', 'http://path/to/image', '#f00'));

dispatch((new SendBearyChat)->client('server')->text('hello')->add('attachment'));

dispatch(new SendBearyChat(
    bearychat('admin')->text('New order!')->add($order, $order->name, $order->image_url)
));
```

### 报告 Laravel 异常

BearyChat 的一个常见用法是实时报告 Laravel 应用的异常或错误日志。要实现这个功能，只需要重载现有的异常处理类中的 `report` 方法，并添加发送异常信息到 BearyChat ：

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

    if (app()->environment('production') && $this->shouldReport($e)) {
        dispatch(
            (new SendBearyChat)
            ->client('server')
            ->text('New Exception!')
            ->notification('New Exception: '.get_class($e))
            ->markdown(false)
            ->add(str_limit($e, 1300), get_class($e), null, '#a0a0a0')
        );
    }
}
```

### 响应 Outgoing

使用 `Message` 对象可以很方便的响应 [Outgoing 机器人][Outgoing]：

```php
Route::post('webhook/bearychat', 'WebhookController@bearychat');
```

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ElfSundae\BearyChat\Message;

class WebhookController extends Controller
{
    /**
     * The BearyChat Outgoing Robot.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bearychat(Request $request)
    {
        $message = (new Message)
            ->text('Response for ' . $request->input('text'))
            ->add('attachment content');

        return response()->json($message);
    }
}
```

为 Outgoing 路由禁用 CSRF 保护，请参考 [Laravel 官方文档][CSRF]。

### 自定义 Guzzle

你可以通过 `BearyChat` 门面或 `app('bearychat')` 的 `customHttpClient` 方法来自定义用于发送 HTTP 请求的 [Guzzle][] client。

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client as HttpClient;
use BearyChat;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        BearyChat::customHttpClient(function ($name) {
            if ($name == 'dev') {
                return new HttpClient([
                    'connect_timeout' => 10,
                    'timeout' => 30,
                    'verify' => false
                ]);
            }
        });
    }
}
```

## 更新日志

详见 [CHANGELOG](CHANGELOG.md) 文件。

## 测试

```sh
$ composer test
```

## 许可协议

BearyChat Laravel 扩展包在 [MIT 许可协议](LICENSE)下提供和使用。

[1]: https://github.com/ElfSundae/BearyChat
[2]: https://github.com/ElfSundae/BearyChat/blob/master/README_zh.md
[Webhook]: https://bearychat.com/integrations/incoming
[Outgoing]: https://bearychat.com/integrations/outgoing
[BearyChat]: https://bearychat.com
[Composer]: https://getcomposer.org
[queue system]: https://laravel.com/docs/queues
[CSRF]: https://laravel.com/docs/5.2/routing#csrf-excluding-uris
[Guzzle]: http://docs.guzzlephp.org
[BearyChatChannel]: https://github.com/laravel-notification-channels/bearychat
