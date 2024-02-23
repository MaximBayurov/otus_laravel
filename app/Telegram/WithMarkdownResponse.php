<?php

namespace App\Telegram;

trait WithMarkdownResponse
{
    protected function escapeForMarkdown(string $string): string
    {
        return addcslashes($string, '.-()\/\>\<\\');
    }
}
