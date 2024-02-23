<?php

namespace App\Telegram\Commands;

use App\Repositories\UsersRepository;
use App\Telegram\AuthUserCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class UnlinkCommand extends AuthUserCommand
{

    /**
     * @var string
     */
    protected $name = 'Отвязать аккаунты';

    /**
     * @var string
     */
    protected $description = 'Отвязывает аккаунты пользователя';

    /**
     * @var string
     */
    protected $usage = '/unlink';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $telegramId = $this->getMessage()->getFrom()->getId();

        /** @var UsersRepository $usersRepository */
        $usersRepository = app()->get(UsersRepository::class);
        $usersRepository->unlinkTelegramId($telegramId);

        $this->isUserAuthed = false;

        return $this->replyToChat("Аккаунты успешно отвязаны", [
            'protect_content' => true,
        ]);
    }

}
