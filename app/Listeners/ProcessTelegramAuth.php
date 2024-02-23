<?php

namespace App\Listeners;

use App\Repositories\UsersRepository;
use App\Services\ITelegramBotService;
use Illuminate\Auth\Events\Login;

class ProcessTelegramAuth
{

    /**
     * Handle the event.
     *
     * @param \Illuminate\Auth\Events\Login $event
     *
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function handle(Login $event)
    {
        /**
         * @var \App\Models\User $user
         */
        $user = $event->user;

        $telegramBot = app()->get(ITelegramBotService::class);
        $authData = $telegramBot->getRememberedAuthData();

        if (!$user || empty($authData) || empty($authData["id"])) {
            return;
        }

        $usersRepository = app()->get(UsersRepository::class);
        $usersRepository->updateTelegramIdFor($user, $authData["id"]);

        session()->flash('flash-message', sprintf(
            "Аккаунты успешно связаны, вы можете <a href='%s'> вернуться обратно к боту</a>",
            $telegramBot->createDeepLink('start')
        ));
    }
}
