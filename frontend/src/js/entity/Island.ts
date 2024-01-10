import { Terrain } from './Terrain.js'

export interface Island {
  id: number
  name: string
  owner_name: string
  comment?: string
  terrains?: Terrain[]
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
