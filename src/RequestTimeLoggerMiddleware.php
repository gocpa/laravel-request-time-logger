<?php

declare(strict_types=1);

namespace GoCPA\LaravelRequestTimeLogger;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Логирование долгих HTTP-запросов
 */
class RequestTimeLoggerMiddleware
{
    public function __construct(private LogManager $logger)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request): void
    {
        if (!$this->scriptExecutionEnabled()) {
            return;
        }

        $loggerChannel = Config::get('gocpa-laravel-request-time-logger.log_channel');
        $slowTimeSeconds = (int) Config::get('gocpa-laravel-request-time-logger.script_execution.slowTimeSeconds');

        $executionTime = microtime(true) - LARAVEL_START;
        if ($executionTime >= $slowTimeSeconds) {
            $logMessage = sprintf('Slow request time: %f s', $executionTime);
            $this->logger
                ->channel($loggerChannel)
                ->warning(
                    $logMessage,
                    [
                        'requestMethod' => $request->getMethod(),
                        'requestUri' => $request->getRequestUri(),
                    ],
                );
        }
    }

    private function scriptExecutionEnabled(): bool
    {
        return (bool) Config::get('gocpa-laravel-request-time-logger.script_execution.slowTimeSeconds', false);
    }
}
