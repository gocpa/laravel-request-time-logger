<?php

declare(strict_types=1);

// config for GoCPA/LaravelRequestTimeLogger
return [

    /**
     * Канал для логирования долгих запросов
     */
    'log_channel' => env('GOCPA_REQUEST_TIME_LOGGER_LOG_CHANNEL', env('LOG_CHANNEL')),

    /**
     * Долгие запросы в БД
     */
    'database_query' => [
        /**
         * Время в секундах для логирования долгих запросов в БД
         */
        'slowTimeSeconds' => env('GOCPA_REQUEST_TIME_LOGGER_DATABASE_QUERY_SECONDS', 1),
    ],

    /**
     * Долгие HTTP-запросы
     */
    'script_execution' => [
        /**
         * Время в секундах для логирования долгих HTTP-запросов
         */
        'slowTimeSeconds' => env('GOCPA_REQUEST_TIME_LOGGER_SCRIPT_EXECUTION_SECONDS', 1),
    ],
];
