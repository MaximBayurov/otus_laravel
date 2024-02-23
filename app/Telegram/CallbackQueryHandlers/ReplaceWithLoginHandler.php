<?php

namespace App\Telegram\CallbackQueryHandlers;

use App\Telegram\CallbackQueryHandler;
use App\Telegram\Commands\StartCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class ReplaceWithLoginHandler extends CallbackQueryHandler
{
    public function execute(): ServerResponse
    {
        ['text' => $text, 'keyboard' => $keyboard] = StartCommand::getLinkData();

        return Request::editMessageText([
            'chat_id' => $this->query->getMessage()->getChat()->getId(),
            'message_id' => $this->query->getMessage()->getMessageId(),
            'text' => $text,
            'reply_markup' => $keyboard,
            'protect_content' => true,
        ]);
    }

}
