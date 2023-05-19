import {defineStore} from "pinia";
import lodash from "lodash";
import {Status} from "./Entity/Status";
import {Hakoniwa} from "./Entity/Hakoniwa";
import {Island} from "./Entity/Island";
import {Terrain} from "./Entity/Terrain";
import {Log} from "./Entity/Log";
import {Plan} from "./Entity/Plan";
import axios from "axios";
import {Point} from "./Entity/Point";
import {Turn} from "./Entity/Turn";
import {Theme} from "./Entity/Theme";

const ISLAND_ENVIRONMENT = {
    'best': '最高',
    'good': '良好',
    'normal': '通常',
}

export interface PiniaState {
    hakoniwa: Hakoniwa,
    island: Island,
    terrains: Terrain[],
    status: Status,
    logs: Array<Log>,
    plans: Plan[],
    sentPlans: Plan[],
    targetIslands: Island[],
    selectedPoint: Point,
    selectedPlanNumber: number,
    selectedAmount: number,
    selectedTargetIsland: number,
    isPlanSent: boolean,
    isSendingPlan: boolean,
    planCandidate: Plan[],
    planSendingResult: number,
    showNotification: boolean,
    turn: Turn,
    theme: Theme,
    isOpenPopup: boolean,
    isLoadingTerrain: boolean,
}

export const useMainStore = defineStore('main', {
    state: (): PiniaState => {
        return {
            hakoniwa: {width: 0, height: 0},
            island: {id: 0, name: '', owner_name: ''},
            terrains: [],
            status: {
                area: 0,
                development_points: 0,
                environment: '',
                foods: 0,
                foods_production_capacity: 0,
                funds: 0,
                funds_production_capacity: 0,
                population: 0,
                resources: 0,
                resources_production_capacity: 0,
                abandonment_turn: 0,
            },
            logs: [],
            plans: [],
            sentPlans: [],
            targetIslands: [],
            selectedPoint: {x: 0, y: 0},
            selectedPlanNumber: 1,
            selectedAmount: 0,
            selectedTargetIsland: 0,
            isPlanSent: true,
            isSendingPlan: false,
            planCandidate: [],
            planSendingResult: 200,
            showNotification: false,
            turn: {
                turn: 0,
                next_time: new Date('1970/1/1 00:00:00')
            },
            theme: {
                name: "light",
                themeClass: "theme-light",
                type: "light"
            },
            isOpenPopup: false,
            isLoadingTerrain: false,
        }
    },
    getters: {
        getEnvironmentString(): string {
            return ISLAND_ENVIRONMENT[this.status.environment];
        },
        getDefaultPlan(): Plan {
            return {
                key: 'cash_flow',
                data: {
                    name: '資金繰り',
                    point: {
                        x: 0,
                        y: 0,
                    },
                    amount: 0,
                    usePoint: false,
                    useAmount: false,
                    useTargetIsland: false,
                    targetIsland: this.island.id,
                    isFiring: false,
                    priceString: '(+10億円)',
                    amountString: '',
                    defaultAmountString: '',
                }
            }
        },
        selectedTargetIslandName(): string {
            const target = this.targetIslands.find(island => island.id === this.selectedTargetIsland);
            return target.name;
        }
    },
    actions: {
        async putPlan() {
            console.debug('PUT', '/api/islands/' + this.island.id + '/plans')
            await axios.put(
                '/api/islands/' + this.island.id + '/plans',
                {
                    plan: JSON.stringify(this.plans),
                }
            )
                .then(res => {
                    // sentPlanSuccess
                    console.debug(res)
                    this.isSendingPlan = false;
                    this.plans = res.data.plan;
                    this.sentPlans = lodash.cloneDeep(this.plans);
                    this.planSendingResult = res.status;
                    this.showNotification = true
                })
                .catch(err => {
                    this.isSendingPlan = false;
                    this.planSendingResult = err.response.status;
                    this.showNotification = true;
                    console.error(err);
                })
        },
        async getIslandTerrain(id: number) {
            this.isLoadingTerrain = true;
            const target = this.targetIslands.filter(island => island.id === id);
            if (target.length < 1) throw new Error("存在しない島IDです");
            if (target.length > 1) throw new Error("targetIslandに島が重複しています");
            // 既にロード済みの場合
            if (target[0].terrains !== undefined) {
                this.isLoadingTerrain = false;
                return;
            }

            console.debug('GET', '/api/islands/' + id)
            await axios.get(
                '/api/islands/' + id,
            )
                .then(res => {
                    target[0].terrains = res.data.island.terrains;
                    this.isLoadingTerrain = false;
                })
                .catch(err => {
                    console.debug(err);
                    throw new Error("島の地形取得時にエラーが発生しました")
                })
        }
    }
})
