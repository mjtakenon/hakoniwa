import { Island } from './Island.js'
import { User } from './User.js'

export type BbsVisibility = 'public' | 'private'

export interface BbsMessage {
  /// メッセージID
  id: number

  /// 送信者
  user: User

  /// 送信されたターン
  turn?: number

  /// 投稿内容
  comment?: string

  visibility: BbsVisibility
  deleted: boolean

  /// BBSメッセージに対して削除などのアクションを行うとき、エラーが発生したときの表示用
  errorMessage?: string
}
