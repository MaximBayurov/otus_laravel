<?php

namespace App\Telegram;

use App\Repositories\UsersRepository;
use Longman\TelegramBot\Commands\UserCommand;

abstract class AuthUserCommand extends UserCommand
{
    protected ?bool $isUserAuthed = null;

    public function showInHelp(): bool
    {
        return $this->isEnabled();
    }

    public function isEnabled(): bool
    {
        if (empty($this->isUserAuthed)) {
            $usersRepository = app()->get(UsersRepository::class);
            $message = $this->getMessage() ?: $this->getCallbackQuery()->getMessage();
            $this->isUserAuthed = !empty($usersRepository->getByTelegramId($message->getChat()->getId()));
        }

        return $this->isUserAuthed;
    }
}
