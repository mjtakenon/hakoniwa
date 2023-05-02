// store.ts
import { InjectionKey } from 'vue'
import { createStore, Store } from 'vuex'
import lodash from "lodash";
import { Plan } from "./Entity/Plan";
import { api } from "./api";
import { Island } from "./Entity/Island";
import { Terrain } from "./Entity/Terrain";
import { Status } from "./Entity/Status";
import { Hakoniwa } from "./Entity/Hakoniwa";
import { Log } from "./Entity/Log";

// ストアのステートに対して型を定義します
export interface State {
    hakoniwa: Hakoniwa,
    island: Island,
    terrains: Array<Terrain>,
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
    planCandidate: object,
    planSendingResult: number,
    showNotification: boolean,
}

// インジェクションキーを定義します
export const key: InjectionKey<Store<State>> = Symbol()

export const store = createStore<State>({
    state: {
        plans: [],
        sentPlans: [],
        selectedPoint: {x: 0, y: 0},
        selectedPlanNumber: 1,
        selectedAmount: 0,
        selectedTargetIsland: 0,
        isPlanSent: true,
        isSendingPlan: false,
        planSendingResult: 200,
        showNotification: false,
        hakoniwa: { width: 0, height: 0 },
        island: { id: 0, name: '', owner_name: ''},
        terrains: [],
        status: { area: 0, development_points: 0, environment: '', foods: 0, foods_production_number_of_people: 0, funds: 0, funds_production_number_of_people: 0, population: 0, resources: 0, resources_production_number_of_people: 0 },
        logs: [],
        planCandidate: {},
        targetIslands: [],
    },
    actions: {
        async putPlan(context, payload) {
            await api.putPlan()
                .then(res => {
                    console.debug(res)
                    context.commit('sentPlanSuccess', res)
                })
                .catch(err => {
                    context.commit('sentPlanFailed', err)
                })
        }
    },
    mutations: {
        sentPlanSuccess(state, res) {
            state.isSendingPlan = false
            state.plans = res.data.plan
            state.sentPlans = lodash.cloneDeep(state.plans)
            state.planSendingResult = res.status
            state.showNotification = true
        },
        sentPlanFailed(state, err) {
            state.isSendingPlan = false
            state.planSendingResult = err.response.status
            state.showNotification = true
            console.log(err)
        },
    }
})
