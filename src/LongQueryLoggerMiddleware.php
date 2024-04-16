<?php

declare(strict_types=1);

namespace GoCPA\LaravelRequestTimeLogger;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Логирование долгих запросов к БД
 */
class LongQueryLoggerMiddleware
{
    public function __construct(private LogManager $logger)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->databaseQueryEnabled()) {
            return $next($request);
        }

        DB::enableQueryLog();

        return $next($request);
    }

    public function terminate(Request $request): void
    {
        if (!$this->databaseQueryEnabled()) {
            return;
        }

        $loggerChannel = Config::get('gocpa-laravel-request-time-logger.log_channel');
        $slowTimeSeconds = (int) Config::get('gocpa-laravel-request-time-logger.database_query.slowTimeSeconds');
        /** @var array $rawQueryLog */
        $rawQueryLog = DB::getRawQueryLog();
        foreach ($rawQueryLog as $rawQueryItem) {
            $executionTime = $rawQueryItem['time'] / 1000;

            if ($executionTime >= $slowTimeSeconds) {
                $logMessage = sprintf('Slow query time: %f s', $executionTime);
                $this->logger
                    ->channel($loggerChannel)
                    ->warning(
                        $logMessage,
                        [
                            'requestMethod' => $request->getMethod(),
                            'requestUri' => $request->getRequestUri(),
                            'raw_query' => $rawQueryItem['raw_query'],
                        ],
                    );
            }
        }
        DB::disableQueryLog();
    }

    private function databaseQueryEnabled(): bool
    {
        return (bool) Config::get('gocpa-laravel-request-time-logger.database_query.slowTimeSeconds', false);
    }
}
