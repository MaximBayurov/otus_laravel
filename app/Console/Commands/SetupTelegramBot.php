<?php

namespace App\Console\Commands;

use App\Services\ITelegramBotService;
use Illuminate\Console\Command;
use Longman\TelegramBot\Exception\TelegramException;

class SetupTelegramBot extends Command
{
    public function __construct(
        private readonly ITelegramBotService $telegramService
    ) {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:tb-setup
                                {hookUrl? : Путь вебхука}
                                {--D|--delete : Удаляет вебхук }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Работает с webhook для телеграм бота';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('delete')) {
            $this->deleteWebhook();

            return;
        }

        $hookUrl = $this->argument('hookUrl');
        if (!empty($hookUrl)) {
            $this->addWebhook($hookUrl);

            return;
        }

        $this->error("Не передан аргумент hookUrl");
    }

    /**
     * Добавляет вебхук для телеграм бота
     *
     * @param string $hookUrl
     *
     * @return void
     */
    private function addWebhook(string $hookUrl): void
    {
        try {
            $result = $this->telegramService->getTelegram()->setWebhook($hookUrl, [
                'secret_token' => $this->telegramService->getSecret(),
            ]);

            if ($result->isOk()) {
                $this->info($result->getDescription());
            }
        } catch (TelegramException $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Удаляет вебхук для телеграм бота
     *
     * @return void
     */
    private function deleteWebhook(): void
    {
        try {
            $result = $this->telegramService->getTelegram()->deleteWebhook();
            $this->info($result->getDescription());
        } catch (TelegramException $e) {
            echo $e->getMessage();
        }
    }
}
