export interface Achievement {
  /// 実績を特定するキー
  type: string

  /// 実績名
  title: string

  /// 実績のタイプ
  category: string

  /// ソートする時の番号
  index: number

  /// 同系統の実績がある場合、実績のグレード（多いほど高級な実績）
  grade: number

  /// 実績のアイコン(FontAwesome準拠、importを忘れないように)
  icon: string

  /// 実績の色（当てるCSSのclass名）
  color: string

  /// ホバー時に表示するテキスト
  hover_text?: string

  /// 実績アイコンの下に表示するテキスト（5文字以下推奨）
  extra_text?: string
}

export interface AchievementProp {
  type: string
  hover_text?: string
  extra_text?: string
}

import AchievementLists from '$js/store/AchievementList.json'

/**
 * 実績に関連するデータををAchievementList.jsonから取得
 * @param data
 */
export const getAchievement = (data: AchievementProp): Achievement => {
  let index = 0

  for (const achieve of AchievementLists) {
    if (data.type === achieve.type) {
      let result = structuredClone(achieve) as Achievement
      result.index = index
      result.hover_text = data.hover_text ?? undefined
      result.extra_text = data.extra_text ?? undefined
      return result
    }
    index++
  }

  return {
    type: data.type,
    title: data.type,
    category: data.type,
    index: 10000,
    grade: 0,
    icon: 'fa-solid fa-circle-question',
    color: 'text-achievement-normal',
    hover_text: data.hover_text ?? undefined,
    extra_text: data.extra_text ?? undefined
  } as Achievement
}

export const getAchievementsList = (data: AchievementProp[]): Achievement[] => {
  const result: Achievement[] = []
  data.forEach((a) => result.push(getAchievement(a)))
  return result
}

/**
 * Achievement.typeが重複しているものから、gradeが最も大きなものだけを返す
 * @param achievements
 */
export const filterDuplicatedAchievementType = (achievements: Achievement[]): Achievement[] => {
  // 配列を追加したり削除したりすると遅くなるので、先にtype一覧を取得
  const categories: Set<string> = new Set()
  achievements.forEach((a) => categories.add(a.category))

  const result: Achievement[] = []

  // 各タイプの中でgradeが最大のものを追加する
  for (const category of categories) {
    let targetIndex = -1
    for (let i = 0; i < achievements.length; i++) {
      if (achievements[i].category !== category) continue

      if (targetIndex === -1) {
        targetIndex = i
      } else if (achievements[targetIndex].grade < achievements[i].grade) {
        targetIndex = i
      }
    }
    result.push(achievements[targetIndex])
  }

  return result
}

export const sortAchievements = (achievements: Achievement[]) => {
  achievements.sort((a, b) => {
    if (a.index < b.index) return -1
    else if (a.index > b.index) return 1
    else return 0
  })
}
