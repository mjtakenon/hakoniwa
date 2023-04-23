// store.ts
import { InjectionKey } from 'vue'
import { createStore, Store } from 'vuex'
import lodash from "lodash";
import { Plan } from "./Plan";
import { api } from "./api";
import { Island } from "./Island";
import { Terrain } from "./Terrain";
import { Status } from "./Status";
import { Hakoniwa } from "./Hakoniwa";
import { Log } from "./Log";

// ストアのステートに対して型を定義します
export interface State {
    hakoniwa: Hakoniwa,
    island: Island,
    terrains: Array<Terrain>,
    status: Status,
    logs: Array<Log>,
    plans: Plan[],
    sentPlans: Plan[],
    selectedPoint: Point,
    selectedPlanNumber: number,
    isPlanSent: boolean,
    isSendingPlan: boolean,
    planCandidate: object,
}

// インジェクションキーを定義します
export const key: InjectionKey<Store<State>> = Symbol()

export const store = createStore<State>({
    state: {
        plans: [],
        sentPlans: [],
        selectedPoint: {x: 0, y: 0},
        selectedPlanNumber: 1,
        isPlanSent: true,
        isSendingPlan: false,
        hakoniwa: { width: 0, height: 0 },
        island: { id: 0, name: '', owner_name: ''},
        terrains: [],
        status: { area: 0, development_points: 0, environment: '', foods: 0, foods_production_number_of_people: 0, funds: 0, funds_production_number_of_people: 0, population: 0, resources: 0, resources_production_number_of_people: 0 },
        logs: [],
        planCandidate: {},
    },
    actions: {
        async putPlan(context, payload) {
            await api.putPlan()
                .then(res => {
                    console.debug(res)
                    context.commit('sentPlan', res.data)
                })
                .catch(err => {
                    store.state.isSendingPlan = false
                    console.error(err)
                })
        }
    },
    mutations: {
        sentPlan(state, payload) {
            state.plans = payload.plan
            state.isSendingPlan = false
            state.sentPlans = lodash.cloneDeep(state.plans)
        }
    }
})
