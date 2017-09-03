# Release Notes

## Unreleased

- Remove support to Laravel 4
- Deferred service provider, you should register Facade alias yourself in `config/app.php`

## 1.1.5 (2017-08-31)

- Support Laravel Package Discovery
- Support config defaults for all clients

## 1.1.4 (2016-11-29)

- Fixed wrong type-hinted dependency in the construction of `ClientManager`. [b238125](https://github.com/ElfSundae/laravel-bearychat/commit/b23812594eacf483922a90d086f5846f7fb1d7d4)

## 1.1.3 (2016-09-26)

- Added tests.
- Updated the example of queued job.
- Fixed the construction of `ClientManager`.

## 1.1.0 (2016-07-11)

- Added a `"default"` config to configure the default client name.
- Moved clients configuration to `"clients"` array.
