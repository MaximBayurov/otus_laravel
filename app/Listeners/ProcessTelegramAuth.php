<?php

namespace App\Listeners;

use App\Repositories\UsersRepository;
use App\Services\ITelegramBotService;
use Illuminate\Auth\Events\Login;

class ProcessTelegramAuth
{
    public function __construct(
        protected ITelegramBotService $telegramBot,
        protected UsersRepository $usersRepository,
    ) {
    }

    /**
     * Handle the event.
     *
     * @param \Illuminate\Auth\Events\Login $event
     *
     * @return void
     */
    public function handle(Login $event)
    {
        /** @var \App\Models\User $user */
        $user = $event->user;
        $authData = $this->telegramBot->getRememberedAuthData();

        if (!$user || empty($authData) || empty($authData["id"])) {
            return;
        }

        $this->usersRepository->updateTelegramIdFor($user, $authData["id"]);

        session()->flash(
            'flash-message',
            sprintf(
                "Аккаунты успешно связаны, вы можете <a href='%s'> вернуться обратно к боту</a>",
                $this->telegramBot->createDeepLink('start')
            )
        );
    }
}
