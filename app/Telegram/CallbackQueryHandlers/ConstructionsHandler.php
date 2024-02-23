<?php

namespace App\Telegram\CallbackQueryHandlers;

use App\Enums\PageSizesEnum;
use App\Telegram\CallbackQueryHandler;
use Domain\ModuleLanguageConstructions\Models\Construction;
use Domain\ModuleLanguageConstructions\Repositories\ConstructionsRepository;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class ConstructionsHandler extends CallbackQueryHandler
{
    public static function getListReply(int $selectedPage): array
    {
        /** @var ConstructionsRepository $constructionsRepository */
        $constructionsRepository = app()->get(ConstructionsRepository::class);
        $constructions = $constructionsRepository->getPagination($selectedPage, PageSizesEnum::SIZE_5);

        $buttons = [];
        /** @var Construction $construction */
        foreach ($constructions as $construction) {
            $buttons[] = [
                [
                    'text' => $construction->getTitle(),
                    'callback_data' => self::getIdentifier() . " " . $construction->getSlug(),
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
                'callback_data' => self::getIdentifier() . " " . $paginationPage,
            ];
        }
        $buttons[] = $paginationButtons;

        $keyboard = new InlineKeyboard(...$buttons);
        $keyboard
            ->setOneTimeKeyboard(true)
            ->setSelective(false);


        return [
            "text" => 'Выберите языковую конструкцию для просмотра более детальной информации',
            "keyboard" => $keyboard,
        ];
    }

    public function execute(): ServerResponse
    {
        $arguments = $this->getArguments();
        $slug = (!empty($arguments) && !is_numeric($arguments[0])) ? $arguments[0] : null;
        $page = (empty($slug) && !empty($arguments)) ? (int) $arguments[0] : null;

        if (empty($slug)) {
            ["text" => $text, "keyboard" => $keyboard] = self::getListReply($page ?: 1);

            return Request::editMessageText([
                'chat_id' => $this->query->getMessage()->getChat()->getId(),
                'message_id' => $this->query->getMessage()->getMessageId(),
                'text' => $text,
                'reply_markup' => $keyboard,
                'protect_content' => true,
            ]);
        }

        /**
         * @var ConstructionsRepository $constructionsRepo
         */
        $constructionsRepo = app()->get(ConstructionsRepository::class);
        $construction = $constructionsRepo->getBySlug($slug);

        $keyboard = new InlineKeyboard(
            [
                [
                    'text' => "В список языковых конструкций",
                    'callback_data' => self::getIdentifier(),
                ],
            ],
        );
        if (!empty($construction) && $construction->languages()->count() > 0) {
            $keyboard->addRow([
                'text' => "Реализации языковой конструкции",
                'callback_data' => ConstImplHandler::getIdentifier() . " " . $construction->getSlug(),
            ]);
        }

        $text = "Не удалось получить информацию о языковой конструкции";
        if (!empty($construction)) {
            $text = $this->escapeForMarkdown(
                sprintf(
                    '*Название*: %s
*Описание*: %s',
                    $construction->getTitle(),
                    $construction->getDescription()
                )
            );
        }

        return Request::editMessageText([
            'chat_id' => $this->query->getMessage()->getChat()->getId(),
            'message_id' => $this->query->getMessage()->getMessageId(),
            'text' => $text,
            'parse_mode' => 'MarkdownV2',
            'reply_markup' => $keyboard,
            'protect_content' => true,
        ]);
    }
}
