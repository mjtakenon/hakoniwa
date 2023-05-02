import {store} from "../store";

export interface Plan {
    key: string,
    data: {
        name: string,
        point: Point,
        amount: number,
        usePoint: boolean,
        useAmount: boolean,
        useTargetIsland: boolean,
        targetIsland: number,
        isFiring: boolean,
        priceString: string,
    },
}

export function getDefaultPlan(): Plan {
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
            targetIsland: store.state.island.id,
            isFiring: false,
            priceString: '(+10億円)',
        }
    }
}
