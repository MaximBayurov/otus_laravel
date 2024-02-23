<?php

namespace App\Telegram;

use App\Telegram\Exceptions\IncorrectCallbackHandlerName;
use Longman\TelegramBot\Entities\CallbackQuery;
use App\Telegram\CallbackQueryHandlers\DefaultHandler;

class CallbackQueryHandlersFactory
{
    public static function create(CallbackQuery $query): CallbackQueryHandler
    {
        $data = explode(" ", $query->getData());
        $identifier = reset($data);

        $class = self::getClassByIdentifier($identifier);
        return new $class($query);
    }

    /**
     * @throws \App\Telegram\Exceptions\IncorrectCallbackHandlerName|\ReflectionException
     */
    public static function getIdentifierByClass(string $class): string
    {
        $className = (new \ReflectionClass($class))->getShortName();
        $pieces = array_filter(preg_split('/(?=[A-Z])/', $className));
        $last = end($pieces);
        if ($last !== "Handler") {
            throw new IncorrectCallbackHandlerName();
        }
        array_pop($pieces);
        return lcfirst(implode("", $pieces));
    }

    public static function getClassByIdentifier(string $identifier): string
    {
        $class = sprintf(
            "\\App\\Telegram\\CallbackQueryHandlers\\%s",
            ucfirst($identifier) . "Handler"
        );
        if (class_exists($class)) {
            return $class;
        }

        return DefaultHandler::class;
    }
}
