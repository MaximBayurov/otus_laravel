<?php

namespace App\Telegram\Commands;

use App\Repositories\UsersRepository;
use App\Telegram\CallbackQueryHandlers\ConstructionsHandler;
use App\Telegram\CallbackQueryHandlers\LanguagesHandler;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\LoginUrl;
use Longman\TelegramBot\Entities\ServerResponse;

class StartCommand extends UserCommand
{
    protected $name = 'Запустить основную команду бота';

    protected $description = 'Команда для запуска бота';

    protected $usage = '/start';

    protected $version = '1.0.0';

    public static function getLinkData(): array
    {
        $keyboard = new InlineKeyboard([
            [
                'text' => 'Связать аккаунты',
                'login_url' => new LoginUrl([
                    "url" => route("tg-link-account"),
                ]),
            ],
        ]);
        $keyboard->setOneTimeKeyboard(true);

        return [
            'text' => 'Для начала работы привяжите свой аккаунт',
            'protect_content' => true,
            'keyboard' => $keyboard,
        ];
    }

    public function execute(): ServerResponse
    {
        $userId = $this->getMessage()?->getFrom()->getId();

        /**
         * @var UsersRepository $usersRepository
         */
        $usersRepository = app()->get(UsersRepository::class);
        $user = !empty($userId) ? $usersRepository->getByTelegramId($userId) : null;
        if ($user) {
            $keyboard = new InlineKeyboard(
                [
                    [
                        'text' => 'Просмотр языковых конструкций',
                        'callback_data' => ConstructionsHandler::getIdentifier(),
                    ],
                ],
                [
                    [
                        'text' => 'Просмотр языков программирования',
                        'callback_data' => LanguagesHandler::getIdentifier(),
                    ],
                ],
            );

            $keyboard
                ->setOneTimeKeyboard(true)
                ->setSelective(false);

            return $this->replyToChat('Выберите команду из списка или через меню', [
                'reply_markup' => $keyboard,
            ]);
        }

        ['text' => $text, 'keyboard' => $keyboard] = self::getLinkData();

        return $this->replyToChat(
            $text,
            [
                'reply_markup' => $keyboard,
                'protect_content' => true,
            ]
        );
    }
}
