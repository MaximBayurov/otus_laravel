<?php

namespace App\Http\Middleware;

use App\Services\ITelegramBotService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TelegramCheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function handle(Request $request, Closure $next): Response
    {
        $data = $request->toArray();

        /**
         * @var ITelegramBotService $telegramBot
         */
        $telegramBot = app()->get(ITelegramBotService::class);
        if ($telegramBot->checkAuthData($data)) {
            $telegramBot->rememberAuthData($data);
        }

        return $next($request);
    }
}
