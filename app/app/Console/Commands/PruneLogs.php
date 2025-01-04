<?php

namespace App\Console\Commands;

use App\Models\Turn;
use Illuminate\Console\Command;

class PruneLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prune:logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '設定を元に過去ログ、島データを削除します。';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Throwable
     */
    public function handle(): int
    {
        \Log::info('start ' . $this->signature);
        $now = hrtime(true);

        // バックアップ間隔が1である場合、prune機能は無効とする
        $validator = \Validator::make([
            'backup_logs_interval' => config('app.hakoniwa.backup_logs_interval'),
            'prune_logs_margin_turn' => config('app.hakoniwa.prune_logs_margin_turn'),
        ], [
            'backup_logs_interval' => 'integer|min:2',
            'prune_logs_margin_turn' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            $this->info('prune設定が無効であるため終了します。');
            \Log::info('end ' . $this->signature . ' ' . hrtime(true) - $now . 'ns');
            return Command::SUCCESS;
        }


        try {
            $latestTurn = Turn::latest()->firstOrFail();

            if ($latestTurn->turn - config('app.hakoniwa.prune_logs_margin_turn') <= 1) {
                $this->info('現在のターンがマージンよりも小さいため終了します。');
                \Log::info('end ' . $this->signature . ' ' . hrtime(true) - $now . 'ns');
                return Command::SUCCESS;
            }

            // 最新のターンからマージンを残し、バックアップ間隔以外のターンを削除
            // 1ターン目は念の為残す
            $turns = Turn::where('turn', '<=', $latestTurn->turn - config('app.hakoniwa.prune_logs_margin_turn'))
                ->whereRaw('turn % ' . config('app.hakoniwa.backup_logs_interval'))
                ->where('turn', '!=', 1)
                ->whereNull('deleted_at')
                ->get();

            foreach ($turns as $turn) {
                \DB::transaction(function () use ($turn) {
                    $turn->lockForUpdate();
                    $turn->islandLogs()->forceDelete();
                    $turn->islandPlans()->forceDelete();
                    $turn->islandStatuses()->forceDelete();
                    $turn->islandTerrains()->forceDelete();
                    $turn->delete();
                });
            }
        } catch (\Exception $exception) {
            if (!is_null(config('app.notification_webhook_url'))) {
                $response = \Http::post(config('app.notification_webhook_url'), [
                    'content' => json_encode(
                        'Failed prune turn. [message: ' . substr($exception->getMessage(), 0, 1000) . ']',
                    ),
                ]);
                if ($response->status() >= 300) {
                    \Log::debug($response->status());
                    \Log::debug($response->body());
                }
            }
            throw $exception;
        }

        \Log::info('end ' . $this->signature . ' ' . hrtime(true) - $now . 'ns');
        return Command::SUCCESS;
    }
}
