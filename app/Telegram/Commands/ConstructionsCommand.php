<?php

namespace App\Telegram\Commands;

use App\Telegram\AuthUserCommand;
use App\Telegram\CallbackQueryHandlers\ConstructionsHandler;
use Longman\TelegramBot\Entities\ServerResponse;

class ConstructionsCommand extends AuthUserCommand
{

    /**
     * @var string
     */
    protected $name = 'Показать список языковых конструкций';

    /**
     * @var string
     */
    protected $description = 'Команда для работы с языковыми конструкциями';

    /**
     * @var string
     */
    protected $usage = '/constructions';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        ["text" => $text, "keyboard" => $keyboard] = ConstructionsHandler::getListReply(1);

        return $this->replyToChat(
            $text,
            [
                'protect_content' => true,
                'reply_markup' => $keyboard,
            ]
        );
    }

}
