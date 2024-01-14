import { Terrain } from './Terrain.js'
import { AchievementProp } from '$entity/Achievement.js'

export interface Island {
  id: number
  name: string
  owner_name: string
  comment?: string
  terrain?: Terrain
}

export interface IslandWithStatuses {
  id: number
  name: string
  owner_name: string
  development_points: number
  funds: number
  foods: number
  resources: number
  population: number
  funds_production_capacity: number
  foods_production_capacity: number
  resources_production_capacity: number
  environment: string
  area: number
  abandoned_turn: number
  comment: string
  achievements: AchievementProp[]
}

export type IslandEnvironment = 'best' | 'good' | 'normal'

export const ISLAND_ENVIRONMENT_STRING = {
  best: '最高',
  good: '良好',
  normal: '通常'
}

export const getEnvironmentString = (environment: IslandEnvironment): string => {
  return ISLAND_ENVIRONMENT_STRING[environment]
}
