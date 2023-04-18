<?php

namespace App\Console\Commands;

use App\Models\Island;
use App\Models\IslandHistory;
use App\Models\IslandLog;
use App\Models\IslandPlan;
use App\Models\IslandStatus;
use App\Models\IslandTerrain;
use App\Models\Turn;
use App\Services\Hakoniwa\Log\AbandonmentLog;
use App\Services\Hakoniwa\Log\ExecuteLog;
use App\Services\Hakoniwa\Log\ILog;
use App\Services\Hakoniwa\Log\Logs;
use App\Services\Hakoniwa\Log\SummaryLog;
use App\Services\Hakoniwa\Plan\AbandonmentPlan;
use App\Services\Hakoniwa\Plan\Plans;
use App\Services\Hakoniwa\Terrain\Terrain;
use App\Services\Hakoniwa\Util\Point;
use Illuminate\Console\Command;

class ExecuteTurn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:turn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ターン処理を実行します。';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info('start ' . $this->signature);
        $now = hrtime(true);

        \DB::transaction(function() {
            $turn = Turn::latest()->firstOrFail();

            // ターン更新
            $newTurn = new Turn();
            $newTurn->turn = $turn->turn+1;
            $newTurn->next_turn_scheduled_at = $turn->next_turn_scheduled_at->addHour();
            $newTurn->save();

            $islands = Island::with([
                    'islandStatuses' => function ($query) use ($turn) {
                        $query->where('turn_id', $turn->id);
                    },
                    'islandTerrains' => function ($query) use ($turn) {
                        $query->where('turn_id', $turn->id);
                    },
                    'islandPlans' => function ($query) use ($turn) {
                        $query->where('turn_id', $turn->id);
                    },
                ])->whereNull('deleted_at')->get();

            // 生産・消費
            /** @var Island $island */
            foreach($islands as $island) {
                $islandPlan = $island->islandPlans->firstOrFail();
                $islandTerrain = $island->islandTerrains->firstOrFail();
                $islandStatus = $island->islandStatuses->firstOrFail();
                $terrain = Terrain::fromJson($islandTerrain->terrain);
                $prevStatus = $islandStatus->toStatus();
                $status = $islandStatus->toStatus();

                // 生産・消費処理
                $status->executeTurn($terrain);

                // コマンド実行
                $plans = Plans::fromJson($islandPlan->plan);
                $executePlanResult = $plans->execute($island, $terrain, $status, $turn);
                $terrain = $executePlanResult->getTerrain();
                $status = $executePlanResult->getStatus();
                $logs = $executePlanResult->getLogs();

                // 災害
                $occurDisasterResult = $terrain->occurDisaster($island, $status, $turn);
                $status = $occurDisasterResult->getStatus();
                $logs->merge($occurDisasterResult->getLogs());

                // セル処理
                // FIXME: 本来災害はセル処理の後だが、隕石→湖判定の順番を考慮し逆にしている
                $terrain->passTime($island, $status);

                // 湖判定
                $terrain->replaceShallowByLake();

                // 災害と湖判定による影響を考慮した再集計
                $status->aggregate($terrain);

                // 人口0による島の放棄
                if ($status->getPopulation() === 0) {
                    $island->deleted_at = now();
                    IslandHistory::createFromIsland($island);
                    $logs = Logs::create();
                    $logs->add(new AbandonmentLog($island, $turn));
                }

                // 集計ログ
                $logs->add(new SummaryLog($status, $prevStatus, $turn));

                // 結果保存
                $newIslandStatus = new IslandStatus();
                $newIslandStatus->turn_id = $newTurn->id;
                $newIslandStatus->island_id = $island->id;
                $newIslandStatus->development_points = $status->getDevelopmentPoints();
                $newIslandStatus->funds = $status->getFunds();
                $newIslandStatus->foods = $status->getFoods();
                $newIslandStatus->resources = $status->getResources();
                $newIslandStatus->population = $status->getPopulation();
                $newIslandStatus->funds_production_number_of_people = $status->getFundsProductionNumberOfPeople();
                $newIslandStatus->foods_production_number_of_people = $status->getFoodsProductionNumberOfPeople();
                $newIslandStatus->resources_production_number_of_people = $status->getResourcesProductionNumberOfPeople();
                $newIslandStatus->environment = $status->getEnvironment();
                $newIslandStatus->area = $status->getArea();
                $newIslandStatus->save();

                $newIslandPlan = new IslandPlan();
                $newIslandPlan->island_id = $island->id;
                $newIslandPlan->turn_id = $newTurn->id;
                $newIslandPlan->plan = $plans->toJson();
                $newIslandPlan->save();

                $newIslandTerrain = new IslandTerrain();
                $newIslandTerrain->island_id = $island->id;
                $newIslandTerrain->turn_id = $newTurn->id;
                $newIslandTerrain->terrain = $terrain->toJson();
                $newIslandTerrain->save();

                /** @var ILog $log */
                foreach ($logs->getLogs() as $log) {
                    $newLog = new IslandLog();
                    $newLog->island_id = $island->id;
                    $newLog->turn_id = $newTurn->id;
                    $newLog->log = $log->get();
                    $newLog->save();
                }
            }

            // 放棄された島はログに入れ、物理削除する
            foreach($islands as $island) {
                if (!is_null($island->deleted_at)) {
                    $island->forceDelete();
                }
            }
        });

        \Log::info('end ' . $this->signature . ' ' . hrtime(true) - $now . 'ns');
        return Command::SUCCESS;
    }
}
