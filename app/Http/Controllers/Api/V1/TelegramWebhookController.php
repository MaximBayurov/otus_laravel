<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ITelegramBotService;
use App\Telegram\CallbackQueryHandlers\ReplaceWithLoginHandler;
use App\Telegram\CallbackQueryHandlersFactory;
use Illuminate\Http\Request;
use Longman\TelegramBot\Commands\SystemCommands\CallbackqueryCommand;
use Longman\TelegramBot\Entities\CallbackQuery;
use Longman\TelegramBot\Exception\TelegramException;

class TelegramWebhookController extends Controller
{
    public function __invoke(ITelegramBotService $botService, Request $request)
    {
        if ($botService->getSecret() !== request()->header('X-Telegram-Bot-Api-Secret-Token')) {
            return;
        }

        try {
            $telegram = $botService->getTelegram();

            CallbackqueryCommand::addCallbackHandler(function (CallbackQuery $query): void {
                if (empty($query->getData())) {
                    return;
                }
                $handler = CallbackQueryHandlersFactory::create($query);

                if (!$handler->isEnabled()) {
                    $handler = new ReplaceWithLoginHandler($query);
                }

                $handler->execute();
            });

            $telegram->handle();

            $message = $request->get('message')
                ?: ($request->get('callback_query')
                    ? $request->get('callback_query')["message"]
                    : null);
            if (!empty($message)) {
                $botService->setCommandsInMenu($message["chat"]["id"]);
            }

        } catch (TelegramException $e) {
            \Log::error($e->getMessage());
        }
    }
}
