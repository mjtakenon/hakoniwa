export interface Plan {
    key: string,
    data: {
        name: string,
        point: Point,
        amount: number,
        use_point: boolean,
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
            amount: 1,
            use_point: false,
        }
    }
}
