<?php

namespace App\Commands;

use App\Services\ImgurService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class UploadCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'upload {path? : 圖片路徑}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = '從剪貼簿上傳圖片';

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

            if (!$this->imgurService->hasLogin() &&
                $this->confirm("未登入時會使用匿名上傳，請問是否要登入？", true)
            ) {
                $this->confirm("auth");
                exit;
            }

            $img = $this->argument('path') ? $this->argument('path') : exec("pbpaste");
            if (!$this->confirm("確定要上傳『{$img}』?", true)) {
                exit;
            }

            $this->info("$img Uploading...");
            $res = $this->imgurService->upload($img);
            $this->imgurService->saveImageLog($res);

            $this->info("Upload completed!");

            $info = [
                'Link'        => "https://imgur.com/{$res['id']}",
                'Direct Link' => $res['link'],
                'Type'        => $res['type'],
                'Markdown'    => "![Imgur]({$res['link']})",
                'HTML'        => "<img src='{$res['link']}' title='Imgur'/>",
            ];
            $this->output->newLine();

            $this->printInfo($info);

            $type = $this->anticipate('複製連結類型？', ['Link', 'Direct Link', 'Markdown', 'HTML', 'no'], "no");
            if ($type !== "no") {
                exec("echo '{$info[$type]}' | pbcopy");
            }
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

    private function printInfo($info)
    {
        $this->comment("=== Image info ===");
        foreach ($info as $key => $value) {
            $this->output->writeln("<fg=magenta>$key</>: <fg=cyan>$value</>");
        }
    }
}
