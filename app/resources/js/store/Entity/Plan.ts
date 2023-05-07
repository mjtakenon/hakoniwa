import {store} from "../store";

export interface Plan {
    key: string,
    data: {
        name: string,
        point?: Point,
        amount: number,
        targetIsland?: number,
        usePoint: boolean,
        useAmount: boolean,
        useTargetIsland: boolean,
        isFiring: boolean,
        priceString: string,
        amountString: string,
        defaultAmountString: string,
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
            amountString: '',
            defaultAmountString: '',
        }
    }
}
