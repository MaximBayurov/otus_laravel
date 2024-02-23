<?php

namespace App\Telegram;

use App\Repositories\UsersRepository;
use Longman\TelegramBot\Entities\CallbackQuery;
use Longman\TelegramBot\Entities\ServerResponse;

abstract class CallbackQueryHandler
{
    use WithMarkdownResponse;

    protected bool $isEnabled;

    public function __construct(protected CallbackQuery $query)
    {
        $usersRepository = app()->get(UsersRepository::class);
        $this->isEnabled = !empty($usersRepository->getByTelegramId($this->query->getMessage()->getChat()->getId()));
    }

    abstract public function execute(): ServerResponse;

    /**
     * @throws \App\Telegram\Exceptions\IncorrectCallbackHandlerName|\ReflectionException
     */
    public static function getIdentifier(): string
    {
        return CallbackQueryHandlersFactory::getIdentifierByClass(static::class);
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    protected function getArguments(): array
    {
        $data = explode(" ", $this->query->getData());
        array_shift($data);
        return $data;
    }
}
