import { Terrain } from './Terrain.js'

export interface Island {
  id: number
  name: string
  owner_name: string
  comment?: string
  terrains?: Terrain[]
}

export const ISLAND_ENVIRONMENT = {
  best: '最高',
  good: '良好',
  normal: '通常'
}
