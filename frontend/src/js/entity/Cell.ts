import {Point} from './Point.js'
import {Edge} from "$entity/Edge.js";

export interface Cell {
  type: CellType
  data: {
    info: string
    point: Point
    sub_type?: string
  }
}

export const CELL_SIZE_Y = 1.325
export const CELL_SIZE_X = 1.732
export const DEFAULT_MODEL_SCALE = 10

export const CELL_PATHS = {
  city: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  factory: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  farm: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  farm_dome: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb', opacity: 0.5 }] },
  forest: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  metropolis: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  mountain: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  volcano: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  mine: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  oilfield: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb', opacity: 0.8}] },
  plain: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  sea: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb', opacity: 0.8}] },
  shallow: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb', opacity: 0.8}] },
  lake: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb', opacity: 0.8}] },
  large_factory: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  town: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  village: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  wasteland: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  missile_base: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  seabed_base: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  park: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  monument_of_agriculture: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  monument_of_mining: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  monument_of_master: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  monument_of_peace: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  monument_of_war: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  monument_of_conquest: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  inora: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  red_inora: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  dark_inora: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  king_inora: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  sanjira: {
    default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }],
    metalized: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }]
  },
  kujira: {
    default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }],
    metalized: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }]
  },
  hamunemu: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  ghost_inora: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  slime: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  slime_legend: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  levinoth: {
    default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }],
    shallow: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }],
    sea: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }]
  },
  begenoth: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  egg: { default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }] },
  transport_ship: {
    default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }],
    shallow: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }],
    sea: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }]
  },
  battleship: {
    default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }],
    shallow: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }],
    sea: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }]
  },
  submarine: {
    default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }],
    shallow: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }],
    sea: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }]
  },
  pirate: {
    default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }],
    shallow: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }],
    sea: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }]
  },
  levinoth_battleship: {
    default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }],
    shallow: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }],
    sea: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }]
  },
  levinoth_submarine: {
    default: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }],
    shallow: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }],
    sea: [{ path: '/img/hakoniwa/hexa/glb/cell/plain.glb' }]
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

export const getRotation = () => {
    return [0, 0, 0]
}

export const getScale = () => {
    return [DEFAULT_MODEL_SCALE, DEFAULT_MODEL_SCALE, DEFAULT_MODEL_SCALE]
}
