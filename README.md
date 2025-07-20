## CSV Upload System

### Stack
- Laravel 12
- Laravel Reverb (Websocket server)
- Laravel Echo (Websocket client)
- Vue
- Redis (Queue, Broadcast)
- League\Csv (CSV manipulate package in PHP)


### Setup
1. Run `composer install`.
2. Rename `.env.example` to `.env`, and generate app key.
3. Run `sail up -d`*.
4. Run `sa queue:work`** at 1 terminal.
5. Run `sa reerb:start`** at another terminal.
6. Simulate user upload files.

*Note: `sail` is alias of `./vendor/bin/sail`.

**Note:: `sa` is alias of `./vendor/bin/sail artisan`.
