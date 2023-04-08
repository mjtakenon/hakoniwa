// store.ts
import { InjectionKey } from 'vue'
import { createStore, Store } from 'vuex'

// ストアのステートに対して型を定義します
export interface State {
    plan: Plan[],
    selectedPoint: Point,
    selectedPlanNumber: number,
}

// インジェクションキーを定義します
export const key: InjectionKey<Store<State>> = Symbol()

export const store = createStore<State>({
    state: {
        plan: [],
        selectedPoint: {x:1, y:1},
        selectedPlanNumber: 1
    }
})
