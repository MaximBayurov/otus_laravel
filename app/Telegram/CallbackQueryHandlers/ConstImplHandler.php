<?php

namespace App\Telegram\CallbackQueryHandlers;

use App\Telegram\CallbackQueryHandler;
use Domain\ModuleLanguageConstructions\Repositories\ConstructionsRepository;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class ConstImplHandler extends CallbackQueryHandler
{
    public function execute(): ServerResponse
    {
        $arguments = $this->getArguments();
        $argumentsCount = count($arguments);

        return match ($argumentsCount) {
            2 => $this->showItem($arguments[0], $arguments[1]),
            1 => $this->showList($arguments[0]),
            default => Request::emptyResponse(),
        };
    }

    private function showItem(string $constSlug, string $langSlug): ServerResponse
    {
        /**
         * @var ConstructionsRepository $constructionsRepo
         */
        $constructionsRepo = app()->get(ConstructionsRepository::class);
        $construction = $constructionsRepo->getBySlug($constSlug);
        $language = $construction->languages()->where('slug', '=', $langSlug)->first();

        $keyboard = new InlineKeyboard([]);

        return Request::editMessageText([
            'chat_id' => $this->query->getMessage()->getChat()->getId(),
            'message_id' => $this->query->getMessage()->getMessageId(),
            'text' => sprintf(
                    'Реализация языковой конструкции *_%s_* в языке программирования *_%s_*:
```%s
%s
```',
                $this->escapeForMarkdown($construction->getTitle()),
                $this->escapeForMarkdown($language->title),
                $language->slug,
                $this->escapeForMarkdown($language->pivot->code)
            ),
            'parse_mode' => 'MarkdownV2',
            'reply_markup' => $keyboard->addRow([
                'text' => "Реализации в языках программирования",
                'callback_data' => self::getIdentifier() . " " . $constSlug,
            ])->addRow([
                'text' => "Другие конструкции в языке программирования",
                'callback_data' => LangConstListHandler::getIdentifier() . " " . $langSlug,
            ])->addRow([
                'text' => "Подробнее о языковой конструкции",
                'callback_data' => ConstructionsHandler::getIdentifier() . " " . $constSlug,
            ])->addRow([
                'text' => "Подробнее о языке программирования",
                'callback_data' => LanguagesHandler::getIdentifier() . " " . $langSlug,
            ])->addRow([
                'text' => "Список языков программирования",
                'callback_data' => LanguagesHandler::getIdentifier(),
            ])->addRow([
                'text' => "Список языковых конструкций",
                'callback_data' => ConstructionsHandler::getIdentifier(),
            ]),
            'protect_content' => true,
        ]);
    }

    private function showList(string $slug): ServerResponse
    {
        /**
         * @var ConstructionsRepository $constructionsRepo
         */
        $constructionsRepo = app()->get(ConstructionsRepository::class);
        $construction = $constructionsRepo->getBySlug($slug);

        $backToListButton = [
            'text' => "В список языковых конструкций",
            'callback_data' => ConstructionsHandler::getIdentifier(),
        ];
        if (empty($construction)) {
            return Request::editMessageText([
                'chat_id' => $this->query->getMessage()->getChat()->getId(),
                'message_id' => $this->query->getMessage()->getMessageId(),
                'text' => "Не удалось найти языковую конструкцию",
                'reply_markup' => new InlineKeyboard($backToListButton),
                'protect_content' => true,
            ]);
        }


        $keyboard = new InlineKeyboard([]);
        foreach ($construction->languages as $language) {
            $keyboard->addRow([
                'text' => $language->title,
                'callback_data' => self::getIdentifier() . " " . $slug . " " . $language->slug,
            ]);
        }

        return Request::editMessageText([
            'chat_id' => $this->query->getMessage()->getChat()->getId(),
            'message_id' => $this->query->getMessage()->getMessageId(),
            'text' => $this->escapeForMarkdown(
                sprintf(
                    "Языковая конструкция *%s* реализована в следующих языках программирования. Выберите язык:",
                    $construction->getTitle()
                )
            ),
            'parse_mode' => 'MarkdownV2',
            'reply_markup' => $keyboard->addRow([
                'text' => "К языковой конструкции",
                'callback_data' => ConstructionsHandler::getIdentifier() . " " . $slug,
            ])->addRow($backToListButton),
            'protect_content' => true,
        ]);
    }
}
