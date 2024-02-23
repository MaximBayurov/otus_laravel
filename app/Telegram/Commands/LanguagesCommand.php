<?php

namespace App\Telegram\Commands;

use App\Telegram\AuthUserCommand;
use App\Telegram\CallbackQueryHandlers\LanguagesHandler;
use Longman\TelegramBot\Entities\ServerResponse;

class LanguagesCommand extends AuthUserCommand
{

    /**
     * @var string
     */
    protected $name = 'Показать список языков программирования';

    /**
     * @var string
     */
    protected $description = 'Команда для работы с языками программирования';

    /**
     * @var string
     */
    protected $usage = '/languages';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        ["text" => $text, "keyboard" => $keyboard] = LanguagesHandler::getListReply(1);

        return $this->replyToChat(
            $text,
            [
                'protect_content' => true,
                'reply_markup' => $keyboard,
            ]
        );
    }
}
