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
import {defaultTheme, Theme} from "./Entity/Theme";
import {AjaxResult, ErrorType, RequestStatus} from "./Entity/Network";
import {Achievement} from "./Entity/Achievement";
import {BbsMessage, BbsVisibility} from "./Entity/Bbs";

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
    hoverWindowX: number,
    hoverWindowY: number,
    isMobile: boolean,
    screenWidth: number,
    showHoverWindow: boolean,
    hoverCellPoint: Point,
    hoverCellType: string,
    hoverCellPath: string,
    turn: Turn,
    theme: Theme,
    isOpenPopup: boolean,
    isLoadingTerrain: boolean,
    user: {
        user_id: number,
        island: Island
    },
    achievements: Achievement[],
    bbs: BbsMessage[],
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
                maintenance_number_of_people: 0,
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
            hoverWindowX: 100,
            hoverWindowY: 100,
            isMobile: false,
            screenWidth: 0,
            showHoverWindow: false,
            hoverCellPoint: {x: 0, y: 0},
            hoverCellType: "sea",
            hoverCellPath: "/img/hakoniwa/gltf/land0.gltf",
            turn: {
                turn: 0,
                next_time: new Date('1970/1/1 00:00:00')
            },
            theme: defaultTheme,
            isOpenPopup: false,
            isLoadingTerrain: false,
            user: {
                user_id: 0,
                island: null,
            },
            achievements: [],
            bbs: []
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
        async postComment(comment: string): Promise<AjaxResult> {
            console.debug('POST', '/api/islands/' + this.island.id + '/comments');
            let result = {} as AjaxResult

            await axios.post(
                '/api/islands/' + this.island.id + '/comments',
                {
                    comment: comment
                }
            ).then(res => {
                this.island.comment = res.data.comment;
                result.status = RequestStatus.Success;
            }).catch(err => {
                console.debug(err);
                result.status = RequestStatus.Failed;
                result.error = err.response.status;
            })
            return result;
        },
        async patchIslandName(name: string, owner: string): Promise<AjaxResult> {
            let result = {} as AjaxResult;
            console.debug('PATCH', '/api/islands/' + this.island.id)
            await axios.patch(
                '/api/islands/' + this.island.id,
                {
                    name: name,
                    owner_name: owner
                }
            ).then(res => {
                result.status = RequestStatus.Success;
                this.user.island.name = res.data.island.name;
                this.user.island.owner_name = res.data.island.owner_name;
                this.status.funds -= 1000;
            }).catch(err => {
                console.debug(err);
                const code = err.response.data.code;
                if (code === "lack_of_funds") {
                    result.error = ErrorType.LackOfFunds;
                } else if (code === "not_changed") {
                    result.error = ErrorType.NotChanged;
                } else if (code === "island_name_duplicated") {
                    result.error = ErrorType.DuplicatedIslandName;
                } else if (code === "owner_name_duplicated") {
                    result.error = ErrorType.DuplicatedOwnerName;
                } else {
                    result.error = ErrorType.Unknown;
                }
            })
            return result;
        },
        async postBbs(comment: string, visibility: BbsVisibility): Promise<AjaxResult> {
            let result = {} as AjaxResult;
            console.debug('POST', '/api/islands/' + this.island.id + '/bbs')
            await axios.post(
                '/api/islands/' + this.island.id + '/bbs',
                {
                    comment: comment,
                    visibility: visibility
                }
            ).then(res => {
                result.status = RequestStatus.Success;
                if (visibility === "private" && this.user.island.id === this.island.id) {
                    this.status.funds -= 1000;
                }
                this.bbs = res.data.bbs;
            }).catch(err => {
                console.debug(err);
                result.status = RequestStatus.Failed;
                const code = err.response.data.code;
                if (code === 'lack_of_funds') {
                    result.error = ErrorType.LackOfFunds;
                } else if (err.response.status === ErrorType.TooManyRequests) { // TODO:enumに含まれているかでstatusを直で入れるようにしたい
                    result.error = ErrorType.TooManyRequests;
                } else if (err.response.status === ErrorType.NotFound) {
                    result.error = ErrorType.NotFound;
                } else {
                    result.error = ErrorType.Unknown;
                }
            })

            return result;
        },
        async deleteBbs(target: BbsMessage): Promise<AjaxResult> {
            let result = {} as AjaxResult;
            console.debug('DELETE', '/api/islands/' + this.island.id + '/bbs/' + target.id);

            await axios.delete(
                '/api/islands/' + this.island.id + '/bbs/' + target.id,
            ).then(res => {
                result.status = RequestStatus.Success;
                this.bbs = res.data.bbs;
            }).catch(err => {
                console.debug(err);
                result.status = RequestStatus.Failed;
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
