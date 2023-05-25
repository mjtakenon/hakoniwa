import {defineStore} from "pinia";
import lodash from "lodash";
import {Status} from "./Entity/Status";
import {Hakoniwa} from "./Entity/Hakoniwa";
import {Island} from "./Entity/Island";
import {Terrain} from "./Entity/Terrain";
import {Log} from "./Entity/Log";
import {Plan} from "./Entity/Plan";
import axios, {AxiosResponse} from "axios";
import {Point} from "./Entity/Point";
import {Turn} from "./Entity/Turn";
import {defaultTheme, Theme} from "./Entity/Theme";

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
    patchIslandNameError: string,
    user: Island,
}

export const useMainStore = defineStore('main', {
    state: (): PiniaState => {
        return {
            hakoniwa: {width: 0, height: 0},
            island: {id: 0, name: '', owner_name: '', comment: ''},
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
            theme: defaultTheme,
            isOpenPopup: false,
            isLoadingTerrain: false,
            patchIslandNameError: "",
            user: {id: 0, name: "", owner_name: ""}
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
                    target[0].comment = res.data.island.comment;
                    this.isLoadingTerrain = false;
                })
                .catch(err => {
                    console.debug(err);
                    throw new Error("島の地形取得時にエラーが発生しました")
                })
        },
        async postComment(comment: string): Promise<AxiosResponse> {
            console.debug('POST', '/api/islands/' + this.island.id + '/comments');
            return await axios.post(
                '/api/islands/' + this.island.id + '/comments',
                {
                    comment: comment
                }
            ).then(res => {
                this.island.comment = res.data.comment;
                console.debug(res);
                return res;
            }).catch(err => {
                console.debug(err);
                return err.response;
            })
        },
        async patchIslandName(name: string, owner: string): Promise<boolean> {
            this.patchIslandNameError = "";
            let result = false;
            console.debug('PATCH', '/api/islands/' + this.island.id)
            await axios.patch(
                '/api/islands/' + this.island.id,
                {
                    name: name,
                    owner_name: owner
                }
            ).then(res => {
                result = true;
                this.user.name = res.data.island.name;
                this.user.owner_name = res.data.island.owner_name;
                this.status.funds -= 1000;
            }).catch(err => {
                this.patchIslandNameError = err.response.data.code;
            })
            return result;
        },
        changeTheme(theme: Theme) {
            const app = document.getElementById("app");
            this.theme = theme;
            app.classList.remove(...app.classList);
            app.classList.add(theme.themeClass);
            app.classList.add(theme.type.toString());
            localStorage.setItem("theme", JSON.stringify(theme));
        }
    }
})
