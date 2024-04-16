# gocpa/laravel-request-time-logger

## Установка
```bash
composer require gocpa/laravel-request-time-logger
php artisan vendor:publish --provider="GoCPA\LaravelRequestTimeLogger\ServiceProvider" --tag=config
```

## Конфигурация
```env
# Сбор логов о долгих HTTP/DB запросах
GOCPA_REQUEST_TIME_LOGGER_LOG_CHANNEL=default
GOCPA_REQUEST_TIME_LOGGER_DATABASE_QUERY_SECONDS=2
GOCPA_REQUEST_TIME_LOGGER_SCRIPT_EXECUTION_SECONDS=4
```

## Сбор данных
Для начала сбора данных по медленным запросам - необходимо подключить middleware.
```php
// ВНИМАНИЕ! Нужно использовать только один вариант подключения сбора данных, иначе записи в лог будут дублироваться для каждого запроса

// 1. Глобально для всех роутов - пропишите в секцию $middleware
protected $middleware = [
    // ...
    \GoCPA\LaravelRequestTimeLogger\LongQueryLoggerMiddleware::class,
    \GoCPA\LaravelRequestTimeLogger\RequestTimeLoggerMiddleware::class,
];

// 2. Только для определенной группы роутов - пропишите в секцию $middlewareGroups
protected $middlewareGroups = [
    'web' => [
        // ...
        \GoCPA\LaravelRequestTimeLogger\LongQueryLoggerMiddleware::class,
        \GoCPA\LaravelRequestTimeLogger\RequestTimeLoggerMiddleware::class,
    ],
];

// 3. Только для определенного роута - пропишите в секцию $middlewareAliases
protected $middlewareAliases = [
    // ...
    'log-long-queries' => \GoCPA\LaravelRequestTimeLogger\LongQueryLoggerMiddleware::class,
    'log-long-requests' => \GoCPA\LaravelRequestTimeLogger\RequestTimeLoggerMiddleware::class,
];
```

## Проверка:
Перейдите на любой долгий роут, если таких нет, можно создать новый со следующим кодом, перейдите на него, проверьте наличие строчек в файле логов. Удалите тестовый роут
```php
Route::middleware(['log-long-queries', 'log-long-requests'])->get('long-route-test', function () {
    \Illuminate\Support\Facades\DB::select('SELECT SLEEP(3)');
    sleep(2);
    return 'ok';
});
```
