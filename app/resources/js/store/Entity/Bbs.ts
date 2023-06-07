import {Island} from "./Island";

export type BbsVisibility = "public" | "private";

export interface BbsMessage {
    /// メッセージID
    id: number

    /// 送信者のユーザID
    user_id: number

    /// 送信されたターン
    turn?: number

    /// 送信者の島情報
    island?: Island

    /// 投稿内容
    comment?: string

    visibility: BbsVisibility
    deleted: boolean

    /// BBSメッセージに対して削除などのアクションを行うとき、エラーが発生したときの表示用
    errorMessage?: string
}

