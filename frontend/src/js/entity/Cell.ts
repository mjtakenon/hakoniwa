import {Point} from './Point.js'

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
  city: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  factory: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  farm: { default: [{ path: '/img/hakoniwa/gltf/farm.gltf' }] },
  farm_dome: { default: [{ path: '/img/hakoniwa/gltf/farm_dome.gltf' }, { path:'/img/hakoniwa/gltf/farm_dome_transparent.gltf', opacity: 0.5 }] },
  forest: { default: [{ path: '/img/hakoniwa/gltf/forest.gltf' }] },
  metropolis: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  mountain: { default: [{ path: '/img/hakoniwa/gltf/mountain.gltf' }] },
  volcano: { default: [{ path: '/img/hakoniwa/gltf/volcano.gltf' }] },
  mine: { default: [{ path: '/img/hakoniwa/gltf/mine.gltf' }] },
  oilfield: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  plain: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  sea: { default: [{ path: '/img/hakoniwa/gltf/sea.gltf' }] },
  shallow: { default: [{ path: '/img/hakoniwa/gltf/shallow.gltf' }] },
  lake: { default: [{ path: '/img/hakoniwa/gltf/shallow.gltf' }] },
  large_factory: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  town: { default: [{ path: '/img/hakoniwa/gltf/village.gltf' }] },
  village: { default: [{ path: '/img/hakoniwa/gltf/village.gltf' }] },
  wasteland: { default: [{ path: '/img/hakoniwa/gltf/wasteland.gltf' }] },
  missile_base: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  seabed_base: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  park: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  monument_of_agriculture: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  monument_of_mining: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  monument_of_master: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  monument_of_peace: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  monument_of_war: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  monument_of_conquest: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  inora: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  red_inora: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  dark_inora: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  king_inora: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  sanjira: {
    default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }],
    metalized: [{ path: '/img/hakoniwa/gltf/plain.gltf' }]
  },
  kujira: {
    default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }],
    metalized: [{ path: '/img/hakoniwa/gltf/plain.gltf' }]
  },
  hamunemu: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  ghost_inora: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  slime: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  slime_legend: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  levinoth: {
    default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }],
    shallow: [{ path: '/img/hakoniwa/gltf/volcano.gltf' }],
    sea: [{ path: '/img/hakoniwa/gltf/wasteland.gltf' }]
  },
  begenoth: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  egg: { default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }] },
  transport_ship: {
    default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }],
    shallow: [{ path: '/img/hakoniwa/gltf/volcano.gltf' }],
    sea: [{ path: '/img/hakoniwa/gltf/wasteland.gltf' }]
  },
  battleship: {
    default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }],
    shallow: [{ path: '/img/hakoniwa/gltf/volcano.gltf' }],
    sea: [{ path: '/img/hakoniwa/gltf/wasteland.gltf' }]
  },
  submarine: {
    default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }],
    shallow: [{ path: '/img/hakoniwa/gltf/volcano.gltf' }],
    sea: [{ path: '/img/hakoniwa/gltf/wasteland.gltf' }]
  },
  pirate: {
    default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }],
    shallow: [{ path: '/img/hakoniwa/gltf/volcano.gltf' }],
    sea: [{ path: '/img/hakoniwa/gltf/wasteland.gltf' }]
  },
  levinoth_battleship: {
    default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }],
    shallow: [{ path: '/img/hakoniwa/gltf/volcano.gltf' }],
    sea: [{ path: '/img/hakoniwa/gltf/wasteland.gltf' }]
  },
  levinoth_submarine: {
    default: [{ path: '/img/hakoniwa/gltf/plain.gltf' }],
    shallow: [{ path: '/img/hakoniwa/gltf/volcano.gltf' }],
    sea: [{ path: '/img/hakoniwa/gltf/wasteland.gltf' }]
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
