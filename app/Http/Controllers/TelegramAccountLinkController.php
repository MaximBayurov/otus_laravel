<?php

namespace App\Http\Controllers;

use App\Repositories\UsersRepository;
use App\Services\ITelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TelegramAccountLinkController extends Controller
{
    public function __construct()
    {
        $this->middleware('tg-check-auth');
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(
        Request $request,
        ITelegramBotService $telegramBotService,
        UsersRepository $usersRepository
    ) {
        $user = Auth::user();
        if ($user) {
            if ($user->telegram_id !== $request->get('id')) {
                $usersRepository->updateTelegramIdFor($user, $request->get('id'));
            }

            return redirect($telegramBotService->createDeepLink("start"));
        }

        return redirect(route('login'))->with(
            'alert-success',
            'Войдите или зарегистрируйтесь и мы сможем связать ваши аккаунты'
        );
    }
}
