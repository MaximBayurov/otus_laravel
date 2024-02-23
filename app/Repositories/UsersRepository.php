<?php

namespace App\Repositories;

use App\Models\User;

class UsersRepository
{
    public function updateTelegramIdFor(User $user, ?string $telegramId = null): void
    {
        $user->telegram_id = $telegramId;
        $user->save();
    }

    public function getByTelegramId(int $userId): ?User
    {
        return User::where('telegram_id', '=', $userId)->first();
    }

    public function unlinkTelegramId(int $telegramId):  void
    {
        User::where('telegram_id', '=', $telegramId)->update(['telegram_id' => null]);
    }
}
