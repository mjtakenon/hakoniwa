// store.ts
import { InjectionKey } from 'vue'
import { createStore, Store } from 'vuex'
import { Plan } from "./Plan";

// ストアのステートに対して型を定義します
export interface State {
    plan: Plan[],
    sentPlan: Plan[],
    selectedPoint: Point,
    selectedPlanNumber: number,
    isPlanSent: boolean,
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
    }
})
