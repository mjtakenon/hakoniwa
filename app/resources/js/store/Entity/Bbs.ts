import {Island} from "./Island";

export type BbsVisibility = "public" | "private";

export interface BbsMessage {
    id: number
    user_id: number
    turn?: number
    island?: Island
    comment?: string
    visibility: BbsVisibility
    deleted: boolean
}

