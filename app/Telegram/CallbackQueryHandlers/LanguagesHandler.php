<?php

namespace App\Telegram\CallbackQueryHandlers;

use App\Enums\PageSizesEnum;
use App\Telegram\CallbackQueryHandler;
use Domain\ModuleLanguageConstructions\Models\Construction;
use Domain\ModuleLanguageConstructions\Repositories\LanguagesRepository;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class LanguagesHandler extends CallbackQueryHandler
{
    public static function getListReply(int $selectedPage): array
    {
        /** @var LanguagesRepository $languagesRepository */
        $languagesRepository = app()->get(LanguagesRepository::class);
        $languages = $languagesRepository->getPagination($selectedPage, PageSizesEnum::SIZE_5);

        $buttons = [];
        /** @var Construction $language */
        foreach ($languages as $language) {
            $buttons[] = [
                [
                    'text' => $language->getTitle(),
                    'callback_data' => self::getIdentifier() . " " . $language->getSlug(),
                ]
            ];
        }

        $paginationButtons = [];
        for ($paginationPage = 1; $paginationPage <= $languages->lastPage(); $paginationPage += 1) {
            if ($paginationPage === $selectedPage) {
                continue;
            }
            $paginationButtons[] = [
                'text' => $paginationPage,
                'callback_data' => self::getIdentifier() . " " . $paginationPage,
            ];
        }
        if (!empty($paginationButtons)) {
            $buttons[] = $paginationButtons;
        }

        $keyboard = new InlineKeyboard(...$buttons);
        $keyboard
            ->setOneTimeKeyboard(true)
            ->setSelective(false);


        return [
            "text" => 'Выберите язык программирования для просмотра более детальной информации',
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
         * @var LanguagesRepository $languagesRepository
         */
        $languagesRepository = app()->get(LanguagesRepository::class);
        $language = $languagesRepository->getBySlug($slug);

        $keyboard = new InlineKeyboard([
            [
                'text' => "Cписок Языков программирования",
                'callback_data' => self::getIdentifier(),
            ],
        ]);
        if (!empty($language) && $language->constructions()->count() > 0) {
            $keyboard->addRow([
                'text' => "Все языковые конструкции данного языка",
                'callback_data' => LangConstListHandler::getIdentifier() . " " . $language->getSlug(),
            ]);
        }

        $text = "Не удалось получить информацию о языке программирования";
        if (!empty($language)) {
            $text = $this->escapeForMarkdown(
                sprintf(
                    '*Название*: %s
*Описание*: %s',
                    $language->getTitle(),
                    $language->getDescription()
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
