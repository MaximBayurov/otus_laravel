<?php

namespace App\Telegram\CallbackQueryHandlers;

use App\Enums\PageSizesEnum;
use App\Repositories\LanguagesRepository;
use App\Telegram\CallbackQueryHandler;
use Domain\ModuleLanguageConstructions\Models\Construction;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class LangConstListHandler extends CallbackQueryHandler
{
    public function execute(): ServerResponse
    {
        $arguments = $this->getArguments();
        $slug = !empty($arguments) && !empty($arguments[0]) ? $arguments[0] : null;
        $selectedPage = !empty($arguments) && !empty($arguments[1]) && is_numeric($arguments[1]) ? (int) $arguments[1] : 1;

        $backToListButton = [
            [
                'text' => "В список Языков программирования",
                'callback_data' => LanguagesHandler::getIdentifier(),
            ]
        ];

        /** @var LanguagesRepository $languageRepository */
        $languageRepository = app(LanguagesRepository::class);
        $language = !empty($slug) ? $languageRepository->getBySlug($slug) : null;

        if (empty($language)) {
            return Request::editMessageText([
                'chat_id' => $this->query->getMessage()->getChat()->getId(),
                'message_id' => $this->query->getMessage()->getMessageId(),
                'text' => 'Не удалось найти язык программирования',
                'reply_markup' => new InlineKeyboard($backToListButton),
                'protect_content' => true,
            ]);
        }

        $constructions = $language->constructions()->paginate(PageSizesEnum::SIZE_5->value, page: $selectedPage);

        $buttons = [];
        /** @var Construction $construction */
        foreach ($constructions as $construction) {
            $buttons[] = [
                [
                    'text' => $construction->title,
                    'callback_data' => ConstImplHandler::getIdentifier() . " " . $construction->slug . " " . $language->getSlug(),
                ]
            ];
        }

        $paginationButtons = [];
        for ($paginationPage = 1; $paginationPage <= $constructions->lastPage(); $paginationPage += 1) {
            if ($paginationPage === $selectedPage) {
                continue;
            }
            $paginationButtons[] = [
                'text' => $paginationPage,
                'callback_data' => self::getIdentifier() . " " . $slug . " " . $paginationPage,
            ];
        }
        $buttons[] = $paginationButtons;
        $buttons[] = [
            [
                'text' => "К языку программирования",
                'callback_data' => LanguagesHandler::getIdentifier() . " " . $slug,
            ]
        ];
        $buttons[] = $backToListButton;

        $keyboard = new InlineKeyboard(...$buttons);
        $keyboard
            ->setOneTimeKeyboard(true)
            ->setSelective(false);

        return Request::editMessageText([
            'chat_id' => $this->query->getMessage()->getChat()->getId(),
            'message_id' => $this->query->getMessage()->getMessageId(),
            'text' => $this->escapeForMarkdown(
                sprintf(
                    'Выберите языковую конструкцию для детального просмотра. Конструкции доступные для языка программирования *%s*:',
                    $language->getTitle(),
                )
            ),
            'reply_markup' => $keyboard,
            'parse_mode' => 'MarkdownV2',
            'protect_content' => true,
        ]);
    }
}
