<?php

namespace App\Commands;

use App\Services\ImgurService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class AuthCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'auth';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Login imgur.';

    private $imgurService;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->imgurService = app()->make(ImgurService::class);

            if (!$this->imgurService->readyUpload() &&
                !$this->confirm("已登入，請問是否要覆蓋登入紀錄？")
            ) {
                return;
            }

            if (!$this->imgurService->hasClientId()) {
                $this->info("請至 Imgur 申請一組 API Client ID 與 Client Secret。");
                $this->line("> https://api.imgur.com/oauth2/addclient");
                $clientID = $this->ask("Client ID: ");
                $clientSecret = $this->ask("Client Secret: ");

                $this->imgurService->setOption($clientID, $clientSecret);
            }

            $this->info("請至這個連結登入Imgur: ");
            $this->line("> " . $this->imgurService->getAuthenticationUrl());
            $code = $this->ask("請輸入登入後的網址: ");
            $this->imgurService->parseAccessToken($code);
            $this->info("Successful!");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
