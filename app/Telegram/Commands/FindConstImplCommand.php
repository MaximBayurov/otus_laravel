<?php

namespace App\Telegram\Commands;

use App\Models\ConstructionLanguage;
use App\Telegram\AuthUserCommand;
use App\Telegram\CallbackQueryHandlers\ConstImplHandler;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;

class FindConstImplCommand extends AuthUserCommand
{
    protected $name = 'Найти реализацию языковой конструкции';

    protected $description = 'Находит реализацию языковой конструкции по переданной строке';

    protected $usage = '/find_const_impl';

    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $query = $this->getMessage()->getText(true);
        if (empty($query)) {
            return $this->replyToChat(
                sprintf('Введите поисковой запрос после команды. Например, %s php', $this->usage),
                [
                    'protect_content' => true,
                ]
            );
        }

        $implementations = ConstructionLanguage::search($query)->take(5)->get();
        if ($implementations->count() === 0) {
            return $this->replyToChat(
                'По вашему запросу ничего не найдено',
                [
                    'protect_content' => true,
                ]
            );
        }


        $keyboard = new InlineKeyboard([]);
        foreach ($implementations as $implementation) {
            $keyboard->addRow([
                'text' => sprintf('%s (%s)',
                    $implementation->construction->title,
                    $implementation->language->title,
                ),
                'callback_data' => ConstImplHandler::getIdentifier() . " " . $implementation->construction->slug . " " . $implementation->language->slug,
            ]);
        }

        return $this->replyToChat(
            trans_choice('telegram.search_result', $implementations->count()),
            [
                'protect_content' => true,
                'reply_markup' => $keyboard
            ]
        );
    }
}
