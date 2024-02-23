<?php

namespace App\Services;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class TelegramBotService implements ITelegramBotService
{

    private Telegram $telegram;

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function __construct(
        private readonly string $botUsername,
        private readonly string $apiKey,
        private readonly ?string $secret,
        private readonly ?string $admin,
        private readonly array $commandsPaths = [],
    ) {
        $this->telegram = new Telegram(
            $this->apiKey,
            $this->botUsername
        );
        $this->telegram->enableAdmins($this->getAdmins());
        $this->telegram->addCommandsPaths($this->getCommandsPaths());
    }

    /**
     * @return string
     */
    public function getBotUsername(): string
    {
        return $this->botUsername;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret ?: '';
    }

    /**
     * @return array
     */
    public function getAdmins(): array
    {
        return $this->admin ? [$this->admin] : [];
    }

    /**
     * @return \Longman\TelegramBot\Telegram
     */
    public function getTelegram(): Telegram
    {
        return $this->telegram;
    }

    /**
     * @param string $command
     * @param string|null $arguments
     *
     * @return string
     */
    public function createDeepLink(string $command, ?string $arguments = null): string
    {
        $arguments = $arguments ?: $command;
        $commandFormatted = sprintf(
            "%s=%s",
            $command,
            $arguments
        );

        return sprintf(
            "https://t.me/%s?%s",
            $this->botUsername,
            $commandFormatted
        );
    }

    public function getCommandsPaths(): array
    {
        return $this->commandsPaths;
    }

    /**
     * Проверяет данные по хэшу и токену бота
     *
     * @link https://core.telegram.org/widgets/login#checking-authorization
     *
     * @param array $data
     *
     * @return bool
     */
    public function checkAuthData(array $data): bool
    {
        if (empty($data) || empty($data["hash"])) {
            return false;
        }

        $hashFromRequest = $data["hash"];
        unset($data["hash"]);

        $checkString = [];
        foreach ($data as $key => $value) {
            $checkString[] = $key . "=" . $value;
        }
        sort($checkString);
        $checkString = implode("\n", $checkString);
        $secret = hash('sha256', $this->apiKey, true);
        $hash = hash_hmac('sha256', $checkString, $secret);

        return !(strcmp($hash, $hashFromRequest) !== 0
            && (time() - $data['auth_date']) > 86400);
    }

    public function rememberAuthData(array $data): void
    {
        session()->put('TELEGRAM_BOT_AUTH_DATA', $data);
    }

    public function getRememberedAuthData(): array
    {
        return session()->get('TELEGRAM_BOT_AUTH_DATA') ?: [];
    }

    /**
     * Устанавливает в меню доступные для пользователя команды
     *
     * @param int $chatId
     *
     * @return void
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function setCommandsInMenu(int $chatId): void
    {
        $commands = array_filter($this->getTelegram()->getCommandsList(), function ($command): bool {
            /*** @var \Longman\TelegramBot\Commands\Command $command */
            return !$command->isSystemCommand()
                && !$command->isAdminCommand()
                && $command->showInHelp()
                && $command->isEnabled();
        });

        /*** @var \Longman\TelegramBot\Commands\Command $command */
        $menuCommands = [];
        foreach ($commands as $command) {
            $menuCommands[] = [
                'command' => $command->getUsage(),
                'description' => $command->getDescription(),
            ];
        }

        Request::setMyCommands([
            'commands' => $menuCommands,
            'scope' => [
                'type' => 'chat',
                'chat_id' => $chatId,
            ],
            'language_code' => 'ru',
        ]);
    }

}
