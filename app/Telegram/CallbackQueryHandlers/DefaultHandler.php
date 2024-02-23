<?php

namespace App\Telegram\CallbackQueryHandlers;

use App\Telegram\CallbackQueryHandler;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class DefaultHandler extends CallbackQueryHandler
{
    public function execute(): ServerResponse
    {
        return Request::sendMessage([
            'chat_id' => $this->query->getMessage()->getChat()->getId(),
            'text' => $this->query->getData(),
            'protect_content' => true,
        ]);
    }

}
