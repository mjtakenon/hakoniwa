import { Point } from './Point.js'

export interface Cell {
  type: CellType
  data: {
    info: string
    point: Point
    sub_type?: string
  }
}

export const DEFAULT_CELL_SIZE = 8

export const CELL_PATHS = {
  city: { default: '/img/hakoniwa/gltf/plain.gltf' },
  factory: { default: '/img/hakoniwa/gltf/plain.gltf' },
  farm: { default: '/img/hakoniwa/gltf/farm.gltf' },
  farm_dome: { default: '/img/hakoniwa/gltf/farm.gltf' },
  forest: { default: '/img/hakoniwa/gltf/forest.gltf' },
  metropolis: { default: '/img/hakoniwa/gltf/plain.gltf' },
  mountain: { default: '/img/hakoniwa/gltf/mountain.gltf' },
  volcano: { default: '/img/hakoniwa/gltf/volcano.gltf' },
  mine: { default: '/img/hakoniwa/gltf/mine.gltf' },
  oilfield: { default: '/img/hakoniwa/gltf/plain.gltf' },
  plain: { default: '/img/hakoniwa/gltf/plain.gltf' },
  sea: { default: '/img/hakoniwa/gltf/sea.gltf' },
  shallow: { default: '/img/hakoniwa/gltf/shallow.gltf' },
  lake: { default: '/img/hakoniwa/gltf/shallow.gltf' },
  large_factory: { default: '/img/hakoniwa/gltf/plain.gltf' },
  town: { default: '/img/hakoniwa/gltf/village.gltf' },
  village: { default: '/img/hakoniwa/gltf/village.gltf' },
  wasteland: { default: '/img/hakoniwa/gltf/wasteland.gltf' },
  missile_base: { default: '/img/hakoniwa/gltf/plain.gltf' },
  seabed_base: { default: '/img/hakoniwa/gltf/plain.gltf' },
  park: { default: '/img/hakoniwa/gltf/plain.gltf' },
  monument_of_agriculture: { default: '/img/hakoniwa/gltf/plain.gltf' },
  monument_of_mining: { default: '/img/hakoniwa/gltf/plain.gltf' },
  monument_of_master: { default: '/img/hakoniwa/gltf/plain.gltf' },
  monument_of_peace: { default: '/img/hakoniwa/gltf/plain.gltf' },
  monument_of_war: { default: '/img/hakoniwa/gltf/plain.gltf' },
  monument_of_conquest: { default: '/img/hakoniwa/gltf/plain.gltf' },
  inora: { default: '/img/hakoniwa/gltf/plain.gltf' },
  red_inora: { default: '/img/hakoniwa/gltf/plain.gltf' },
  dark_inora: { default: '/img/hakoniwa/gltf/plain.gltf' },
  king_inora: { default: '/img/hakoniwa/gltf/plain.gltf' },
  sanjira: {
    default: '/img/hakoniwa/gltf/plain.gltf',
    metalized: '/img/hakoniwa/gltf/plain.gltf'
  },
  kujira: {
    default: '/img/hakoniwa/gltf/plain.gltf',
    metalized: '/img/hakoniwa/gltf/plain.gltf'
  },
  hamunemu: { default: '/img/hakoniwa/gltf/plain.gltf' },
  ghost_inora: { default: '/img/hakoniwa/gltf/plain.gltf' },
  slime: { default: '/img/hakoniwa/gltf/plain.gltf' },
  slime_legend: { default: '/img/hakoniwa/gltf/plain.gltf' },
  levinoth: {
    default: '/img/hakoniwa/gltf/plain.gltf',
    shallow: '/img/hakoniwa/gltf/volcano.gltf',
    sea: '/img/hakoniwa/gltf/wasteland.gltf'
  },
  begenoth: { default: '/img/hakoniwa/gltf/plain.gltf' },
  egg: { default: '/img/hakoniwa/gltf/plain.gltf' },
  transport_ship: {
    default: '/img/hakoniwa/gltf/plain.gltf',
    shallow: '/img/hakoniwa/gltf/volcano.gltf',
    sea: '/img/hakoniwa/gltf/wasteland.gltf'
  },
  battleship: {
    default: '/img/hakoniwa/gltf/plain.gltf',
    shallow: '/img/hakoniwa/gltf/volcano.gltf',
    sea: '/img/hakoniwa/gltf/wasteland.gltf'
  },
  submarine: {
    default: '/img/hakoniwa/gltf/plain.gltf',
    shallow: '/img/hakoniwa/gltf/volcano.gltf',
    sea: '/img/hakoniwa/gltf/wasteland.gltf'
  },
  pirate: {
    default: '/img/hakoniwa/gltf/plain.gltf',
    shallow: '/img/hakoniwa/gltf/volcano.gltf',
    sea: '/img/hakoniwa/gltf/wasteland.gltf'
  },
  levinoth_battleship: {
    default: '/img/hakoniwa/gltf/plain.gltf',
    shallow: '/img/hakoniwa/gltf/volcano.gltf',
    sea: '/img/hakoniwa/gltf/wasteland.gltf'
  },
  levinoth_submarine: {
    default: '/img/hakoniwa/gltf/plain.gltf',
    shallow: '/img/hakoniwa/gltf/volcano.gltf',
    sea: '/img/hakoniwa/gltf/wasteland.gltf'
  }
} as const

export type CellType =
  | 'city'
  | 'factory'
  | 'farm'
  | 'farm_dome'
  | 'forest'
  | 'metropolis'
  | 'mountain'
  | 'volcano'
  | 'mine'
  | 'oilfield'
  | 'plain'
  | 'sea'
  | 'shallow'
  | 'lake'
  | 'large_factory'
  | 'town'
  | 'village'
  | 'wasteland'
  | 'missile_base'
  | 'seabed_base'
  | 'park'
  | 'monument_of_agriculture'
  | 'monument_of_mining'
  | 'monument_of_master'
  | 'monument_of_peace'
  | 'monument_of_war'
  | 'monument_of_conquest'
  | 'inora'
  | 'red_inora'
  | 'dark_inora'
  | 'king_inora'
  | 'sanjira'
  | 'kujira'
  | 'hamunemu'
  | 'ghost_inora'
  | 'slime'
  | 'slime_legend'
  | 'levinoth'
  | 'begenoth'
  | 'egg'
  | 'transport_ship'
  | 'battleship'
  | 'submarine'
  | 'pirate'
  | 'levinoth_battleship'
  | 'levinoth_submarine'

export const getCellTypes = () => {
  return Object.keys(CELL_PATHS)
}

export const getCellSubTypes = (type: CellType) => {
  return Object.keys(CELL_PATHS[type])
}

export const getCellPath = (type: CellType, subType: string | null = null) => {
  return subType ? CELL_PATHS[type][subType] : CELL_PATHS[type].default
}
