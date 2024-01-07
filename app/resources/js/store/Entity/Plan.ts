import { Point } from './Point'

export interface Plan {
  key: string
  data: {
    name: string
    point?: Point
    amount: number
    targetIsland?: number
    usePoint: boolean
    useAmount: boolean
    useTargetIsland: boolean
    isFiring: boolean
    priceString: string
    amountString: string
    defaultAmountString: string
  }
}
