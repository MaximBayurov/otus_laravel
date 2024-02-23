<?php

/**
 * This file is part of the PHP Telegram Bot example-bot package.
 * https://github.com/php-telegram-bot/example-bot/
 *
 * (c) PHP Telegram Bot Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * User "/help" command
 *
 * Command that lists all available commands and displays them in User and Admin sections.
 */

namespace App\Telegram\Commands;

use App\Services\ITelegramBotService;
use App\Telegram\WithMarkdownResponse;
use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;

class HelpCommand extends UserCommand
{
    use WithMarkdownResponse;

    /**
     * @var string
     */
    protected $name = 'Показать список команд';

    /**
     * @var string
     */
    protected $description = 'Показывает список доступных команд';

    /**
     * @var string
     */
    protected $usage = '/help';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * Main command execution
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $commandStr = trim($message->getText(true));

        $telegramBot = app()->get(ITelegramBotService::class);
        $safeToShow = in_array($message->getFrom()->getId(), $telegramBot->getAdmins());

        [$allCommands, $userCommands, $adminCommands] = $this->getUserAndAdminCommands();

        if (empty($commandStr)) {
            $text = '*Список доступных команд*:' . PHP_EOL;
            foreach ($userCommands as $userCommand) {
                $text .= addcslashes($this->escapeForMarkdown($userCommand->getUsage()), '*_') . ' \\- ' . $userCommand->getDescription() . PHP_EOL;
            }

            if ($safeToShow && count($adminCommands) > 0) {
                $text .= PHP_EOL . '*Администраторские команды*:' . PHP_EOL;
                foreach ($adminCommands as $adminCommand) {
                    $text .= addcslashes($this->escapeForMarkdown($adminCommand->getUsage()), '*_') . ' \\- ' . $adminCommand->getDescription() . PHP_EOL;
                }
            }

            $text .= PHP_EOL . $this->escapeForMarkdown('Для получения информации о конкретной команде, введите /help <command>');

            return $this->replyToChat($text, [
                'parse_mode' => 'MarkdownV2',
                'protect_content' => true,
            ]);
        }

        $commandStr = str_replace('/', '', $commandStr);
        if (isset($allCommands[$commandStr]) && ($safeToShow || !$allCommands[$commandStr]->isAdminCommand())) {
            $command = $allCommands[$commandStr];

            return $this->replyToChat(
                sprintf(
                    '*Название*: %s \\(v%s\\)' . PHP_EOL .
                    '*Описание*: %s' . PHP_EOL .
                    '*Команда*: %s',
                    $command->getName(),
                    $this->escapeForMarkdown($command->getVersion()),
                    $command->getDescription(),
                    addcslashes($command->getUsage(), '_'),
                ),
                [
                    'protect_content' => true,
                    'parse_mode' => 'MarkdownV2',
                ]
            );
        }

        return $this->replyToChat(
            $this->escapeForMarkdown('Нет доступной информации: команда `/' . $commandStr . '` не найдена'),
            [
                'protect_content' => true,
                'parse_mode' => 'MarkdownV2',
            ]
        );
    }

    /**
     * Get all available User and Admin commands to display in the help list.
     *
     * @return Command[][]
     * @throws TelegramException
     */
    protected function getUserAndAdminCommands(): array
    {
        /** @var Command[] $commandsList */
        $commandsList = $this->telegram->getCommandsList();

        // Only get enabled Admin and User commands that are allowed to be shown.
        $commands = array_filter($commandsList, function ($command): bool {
            return !$command->isSystemCommand() && $command->showInHelp() && $command->isEnabled();
        });

        // Filter out all User commands
        $userCommands = array_filter($commands, function ($command): bool {
            return $command->isUserCommand();
        });

        // Filter out all Admin commands
        $adminCommands = array_filter($commands, function ($command): bool {
            return $command->isAdminCommand();
        });

        ksort($commands);
        ksort($userCommands);
        ksort($adminCommands);

        return [$commands, $userCommands, $adminCommands];
    }
}
