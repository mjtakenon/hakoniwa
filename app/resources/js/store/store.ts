// store.ts
import { InjectionKey } from 'vue'
import { createStore, Store } from 'vuex'
import { Plan } from "./Plan";
import { api } from "./api";

// ストアのステートに対して型を定義します
export interface State {
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
        selectedPoint: {x:0, y:0},
        selectedPlanNumber: 1,
        isPlanSent: true,
        isSendingPlan: false,
    },
    actions: {
        async sendPlan(context, payload) {
            await api.sendPlan()
                .then(res => {
                    context.commit('sendPlan', res.data)
                })
                .catch(err => {
                    store.state.isSendingPlan = false
                    console.error(err)
                })
        }
    },
    mutations: {
        sendPlan(state, payload) {
            store.state.isSendingPlan = false
            store.state.sentPlan = store.state.plan
            // console.log('mutations')
            // state.user.name = payload.name // ➂stateの更新
        }
    }
})
