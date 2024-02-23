<?php

namespace App\Services;

use App\Models\User;
use Longman\TelegramBot\Telegram;

interface ITelegramBotService
{
    public function getTelegram(): Telegram;

    public function getAdmins(): array;

    public function getSecret(): string;

    public function getApiKey(): string;

    public function getBotUsername(): string;

    public function createDeepLink(string $command, ?string $arguments = null): string;

    public function getCommandsPaths(): array;

    public function checkAuthData(array $data): bool;

    public function rememberAuthData(array $data): void;

    public function getRememberedAuthData(): array;

    public function setCommandsInMenu(int $chatId): void;

}
