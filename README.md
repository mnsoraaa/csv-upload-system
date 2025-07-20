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
2. Run `cp .env.example .env`.
3. Run `php artisan key:generate`.
4. Run `npm install && npm run build`.
3. Run `sail up -d`*.
4. Run `sa queue:work`** at a terminal.
5. Run `sa reerb:start`** at another terminal.
6. Simulate user upload files.

*Note: `sail` is alias of `./vendor/bin/sail`. <br>
**Note: `sa` is alias of `./vendor/bin/sail artisan`.
