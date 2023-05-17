<?php

namespace App\Console\Commands;

use App\Entity\Event\Disaster\Disaster;
use App\Entity\Event\ForeignIsland\ForeignIslandEvent;
use App\Entity\Log\AbandonmentLog;
use App\Entity\Log\AbortInvalidIslandLog;
use App\Entity\Log\InviteNewImmigrationLog;
use App\Entity\Log\LogRow;
use App\Entity\Log\Logs;
use App\Entity\Log\UnpopulatedIslandLog;
use App\Entity\Plan\ForeignIsland\TargetedToForeignIslandPlan;
use App\Entity\Plan\Plans;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\IslandHistory;
use App\Models\IslandLog;
use App\Models\IslandPlan;
use App\Models\IslandStatus;
use App\Models\IslandTerrain;
use App\Models\Turn;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

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

        try {
            \DB::transaction(function () {
                $turn = Turn::latest()->firstOrFail();

                // ターン更新
                $newTurn = new Turn();
                $newTurn->turn = $turn->turn + 1;
                $newTurn->next_turn_scheduled_at = now()->addMinutes(config('app.hakoniwa.turn_update_minutes'));
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

                $planList = new Collection();
                $terrainList = new Collection();
                $statusList = new Collection();
                $prevStatusList = new Collection();
                $logsList = new Collection();
                $foreignIslandTargetedPlans = new Collection();
                $foreignIslandEvents = new Collection();

                /** @var Island $island */
                foreach ($islands as $island) {
                    $islandPlan = $island->islandPlans->firstOrFail();
                    $islandTerrain = $island->islandTerrains->firstOrFail();
                    $islandStatus = $island->islandStatuses->firstOrFail();

                    $planList->put($island->id, $islandPlan->toEntity());
                    $terrainList->put($island->id, $islandTerrain->toEntity());
                    $statusList->put($island->id, $islandStatus->toEntity());
                    $prevStatusList->put($island->id, $islandStatus->toEntity());
                    $logsList->put($island->id, Logs::create());
                }

                foreach ($islands as $island) {
                    /** @var Plans $plans */
                    $plans = $planList->get($island->id);
                    /** @var Terrain $terrain */
                    $terrain = $terrainList->get($island->id);
                    /** @var Status $status */
                    $status = $statusList->get($island->id);
                    /** @var Logs $logs */
                    $logs = $logsList->get($island->id);

                    // 生産・消費処理
                    $status->executeTurn($terrain, $island);

                    // コマンド実行
                    $executePlanResult = $plans->execute($island, $terrain, $status, $turn, $foreignIslandTargetedPlans);
                    $terrain = $executePlanResult->getTerrain();
                    $status = $executePlanResult->getStatus();
                    $logs->merge($executePlanResult->getLogs());

                    $planList->put($island->id, $plans);
                    $terrainList->put($island->id, $terrain);
                    $statusList->put($island->id, $status);
                    $logsList->put($island->id, $logs);
                }

                // 他の島を目標にした計画の実行
                /** @var TargetedToForeignIslandPlan $plan */
                foreach ($foreignIslandTargetedPlans as $plan) {
                    /** @var Island $toIsland */
                    $toIsland = $islands->find($plan->getToIsland());
                    /** @var Island $fromIsland */
                    $fromIsland = $islands->find($plan->getFromIsland());
                    /** @var Terrain $fromTerrain */
                    $fromTerrain = $terrainList->get($plan->getFromIsland());
                    /** @var Terrain $toTerrain */
                    $toTerrain = $terrainList->get($plan->getToIsland());
                    /** @var Status $fromStatus */
                    $fromStatus = $statusList->get($plan->getFromIsland());
                    /** @var Status $toStatus */
                    $toStatus = $statusList->get($plan->getToIsland());
                    /** @var Logs $fromLogs */
                    $fromLogs = $logsList->get($plan->getFromIsland());
                    /** @var Logs $toLogs */
                    $toLogs = $logsList->get($plan->getToIsland());

                    if (is_null($toIsland)) {
                        $fromLogs->add(new AbortInvalidIslandLog($island, $plan->getPlan()));
                        $logsList->put($plan->getFromIsland(), $fromLogs);
                        continue;
                    }

                    $executePlanResult = $plan->execute($fromIsland, $toIsland, $fromTerrain, $toTerrain, $fromStatus, $toStatus, $turn);

                    $fromLogs->merge($executePlanResult->getFromLogs());
                    $toLogs->merge($executePlanResult->getToLogs());

                    $terrainList->put($plan->getFromIsland(), $executePlanResult->getFromTerrain());
                    $terrainList->put($plan->getToIsland(), $executePlanResult->getToTerrain());
                    $statusList->put($plan->getFromIsland(), $executePlanResult->getFromStatus());
                    $statusList->put($plan->getToIsland(), $executePlanResult->getToStatus());
                    $logsList->put($plan->getFromIsland(), $fromLogs);
                    $logsList->put($plan->getToIsland(), $toLogs);
                }

                // 災害判定、各セルターン処理
                foreach ($islands as $island) {
                    /** @var Terrain $terrain */
                    $terrain = $terrainList->get($island->id);
                    /** @var Status $status */
                    $status = $statusList->get($island->id);
                    /** @var Logs $logs */
                    $logs = $logsList->get($island->id);

                    // 災害
                    $disasterResult = Disaster::occur($island, $terrain, $status, $turn);
                    $status = $disasterResult->getStatus();
                    $logs->merge($disasterResult->getLogs());

                    // セル処理
                    // FIXME: 本来災害はセル処理の後だが、隕石→湖判定の順番を考慮し逆にしている
                    $passTurnResult = $terrain->passTurn($island, $status, $turn, $foreignIslandEvents);
                    $status = $passTurnResult->getStatus();
                    $logs->merge($passTurnResult->getLogs());

                    // 湖判定
                    $terrain->replaceShallowToLake();

                    // 災害と湖判定による影響を考慮した再集計
                    $status->aggregate($terrain);

                    // 人口0の場合、発展ポイントを減らして村を生成
                    if ($status->getPopulation() === 0) {
                        $terrain->inviteNewImmigration($status);
                        $logs->add(new InviteNewImmigrationLog());
                        $logs->add(new UnpopulatedIslandLog($island));
                        $status->aggregate($terrain);
                    }

                    // 一定ターン以上資金繰りが続いた場合、放棄する
                    if (!is_null(config('app.hakoniwa.island_abandon_turn')) && $status->getAbandonedTurn() >= config('app.hakoniwa.island_abandon_turn')) {
                        $island->deleted_at = now();
                        IslandHistory::createFromIsland($island);
                        $logs = Logs::create();
                        $logs->add(new AbandonmentLog($island));
                    }

                    $terrainList->put($island->id, $terrain);
                    $statusList->put($island->id, $status);
                    $logsList->put($island->id, $logs);
                }

                // セル処理によって発生した計画の実行
                /** @var ForeignIslandEvent $plan */
                foreach ($foreignIslandEvents as $plan) {
                    /** @var Island $toIsland */
                    $toIsland = $islands->find($plan->getToIsland());
                    /** @var Island $fromIsland */
                    $fromIsland = $islands->find($plan->getFromIsland());
                    /** @var Terrain $fromTerrain */
                    $fromTerrain = $terrainList->get($plan->getFromIsland());
                    /** @var Terrain $toTerrain */
                    $toTerrain = $terrainList->get($plan->getToIsland());
                    /** @var Status $fromStatus */
                    $fromStatus = $statusList->get($plan->getFromIsland());
                    /** @var Status $toStatus */
                    $toStatus = $statusList->get($plan->getToIsland());
                    /** @var Logs $fromLogs */
                    $fromLogs = $logsList->get($plan->getFromIsland());
                    /** @var Logs $toLogs */
                    $toLogs = $logsList->get($plan->getToIsland());

                    $executePlanResult = $plan->execute($fromIsland, $toIsland, $fromTerrain, $toTerrain, $fromStatus, $toStatus, $turn);

                    $fromLogs->merge($executePlanResult->getFromLogs());
                    $terrainList->put($plan->getFromIsland(), $executePlanResult->getFromTerrain());
                    $statusList->put($plan->getFromIsland(), $executePlanResult->getFromStatus());
                    $logsList->put($plan->getFromIsland(), $fromLogs);

                    if (!is_null($toLogs)) {
                        $toLogs->merge($executePlanResult->getToLogs());
                    }

                    $terrainList->put($plan->getToIsland(), $executePlanResult->getToTerrain());
                    $statusList->put($plan->getToIsland(), $executePlanResult->getToStatus());
                    $logsList->put($plan->getToIsland(), $toLogs);
                }

                // 集計、保存
                foreach ($islands as $island) {
                    /** @var Plans $plans */
                    $plans = $planList->get($island->id);
                    /** @var Terrain $terrain */
                    $terrain = $terrainList->get($island->id);
                    /** @var Status $status */
                    $status = $statusList->get($island->id);
                    /** @var Status $prevStatus */
                    $prevStatus = $prevStatusList->get($island->id);
                    /** @var Logs $logs */
                    $logs = $logsList->get($island->id);

                    $status->truncateOverflows();

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
                    $newIslandStatus->abandoned_turn = $status->getAbandonedTurn();
                    $newIslandStatus->save();

                    $newIslandPlan = new IslandPlan();
                    $newIslandPlan->island_id = $island->id;
                    $newIslandPlan->turn_id = $newTurn->id;
                    $newIslandPlan->plan = $plans->toJson();
                    $newIslandPlan->save();

                    $newIslandTerrain = new IslandTerrain();
                    $newIslandTerrain->island_id = $island->id;
                    $newIslandTerrain->turn_id = $newTurn->id;
                    $newIslandTerrain->terrain = $terrain->toJson(true, false);
                    $newIslandTerrain->save();

                    /** @var LogRow $log */
                    foreach ($logs->getLogs() as $log) {
                        $newLog = new IslandLog();
                        $newLog->island_id = $island->id;
                        $newLog->turn_id = $newTurn->id;
                        $newLog->log = $log->generate();
                        $newLog->visibility = $log->getVisibility();
                        $newLog->save();
                    }
                }

                // 放棄された島はログに入れ、物理削除する
                foreach ($islands as $island) {
                    if (!is_null($island->deleted_at)) {
                        $island->forceDelete();
                    }
                }
            });
        } catch (\Exception $exception) {
            if (!is_null(config('app.notification_webhook_url'))) {
                $response = \Http::post(config('app.notification_webhook_url'), [
                    'content' => json_encode(
                        'Failed update turn. [message: ' . substr($exception->getMessage(), 0, 1000) . ']',
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
