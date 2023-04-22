// store.ts
import { InjectionKey } from 'vue'
import { createStore, Store } from 'vuex'
import { Plan } from "./Plan";
import { api } from "./api";
import { Island } from "./Island";
import lodash from "lodash";
import { Terrain } from "./Terrain";
import { IslandStatus } from "./IslandStatus";
import { Hakoniwa } from "./Hakoniwa";
import { IslandLog } from "./Log";

// ストアのステートに対して型を定義します
export interface State {
    hakoniwa: Hakoniwa,
    island: Island,
    islandTerrain: Array<Terrain>,
    islandStatus: IslandStatus,
    islandLog: Array<IslandLog>,
    plan: Plan[],
    sentPlan: Plan[],
    selectedPoint: Point,
    selectedPlanNumber: number,
    isPlanSent: boolean,
    isSendingPlan: boolean,
}

// インジェクションキーを定義します
export const key: InjectionKey<Store<State>> = Symbol()

export const store = createStore<State>({
    state: {
        plan: [],
        sentPlan: [],
        selectedPoint: {x: 0, y: 0},
        selectedPlanNumber: 1,
        isPlanSent: true,
        isSendingPlan: false,
        hakoniwa: { width: 0, height: 0 },
        island: { id: 0, name: '', owner_name: ''},
        islandTerrain: [],
        islandStatus: { area: 0, development_points: 0, environment: '', foods: 0, foods_production_number_of_people: 0, funds: 0, funds_production_number_of_people: 0, population: 0, resources: 0, resources_production_number_of_people: 0 },
        islandLog: [],
    },
    actions: {
        async sendPlan(context, payload) {
            await api.sendPlan()
                .then(res => {
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
            console.log(payload)
            store.state.plan = JSON.parse(payload.plan);
            store.state.isSendingPlan = false
            store.state.sentPlan = lodash.cloneDeep(store.state.plan)
            // console.log('mutations')
            // state.user.name = payload.name // ➂stateの更新
        }
    }
})
