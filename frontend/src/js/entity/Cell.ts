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

export const CELL_SIZE_X = 30
export const CELL_SIZE_Y = 32
export const CELL_MARGIN_X = 0
export const CELL_MARGIN_Y = 8
export const DEFAULT_MODEL_SCALE = 0.5

export const CELL_PATHS = {
  city: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  factory: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  farm: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  farm_dome: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }, { path:'/img/hakoniwa/hexa/gltf/cell/wasteland.gltf', opacity: 0.5 }] },
  forest: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  metropolis: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  mountain: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  volcano: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  mine: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  oilfield: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }, { path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf', opacity: 0.8}] },
  plain: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  sea: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/sea.gltf' }, { path: '/img/hakoniwa/hexa/gltf/cell/sea.gltf', opacity: 0.8}] },
  shallow: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/sea.gltf' }, { path: '/img/hakoniwa/hexa/gltf/cell/sea.gltf', opacity: 0.8}] },
  lake: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/sea.gltf' }, { path: '/img/hakoniwa/hexa/gltf/cell/sea.gltf', opacity: 0.8}] },
  large_factory: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  town: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  village: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  wasteland: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  missile_base: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  seabed_base: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  park: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  monument_of_agriculture: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  monument_of_mining: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  monument_of_master: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  monument_of_peace: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  monument_of_war: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  monument_of_conquest: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  inora: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  red_inora: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  dark_inora: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  king_inora: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  sanjira: {
    default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }],
    metalized: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }]
  },
  kujira: {
    default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }],
    metalized: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }]
  },
  hamunemu: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  ghost_inora: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  slime: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  slime_legend: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  levinoth: {
    default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }],
    shallow: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }],
    sea: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }]
  },
  begenoth: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  egg: { default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }] },
  transport_ship: {
    default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }],
    shallow: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }],
    sea: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }]
  },
  battleship: {
    default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }],
    shallow: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }],
    sea: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }]
  },
  submarine: {
    default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }],
    shallow: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }],
    sea: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }]
  },
  pirate: {
    default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }],
    shallow: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }],
    sea: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }]
  },
  levinoth_battleship: {
    default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }],
    shallow: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }],
    sea: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }]
  },
  levinoth_submarine: {
    default: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }],
    shallow: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }],
    sea: [{ path: '/img/hakoniwa/hexa/gltf/cell/wasteland.gltf' }]
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
    return [0, Math.PI/2, 0]
}

export const getScale = () => {
    return [DEFAULT_MODEL_SCALE, DEFAULT_MODEL_SCALE, DEFAULT_MODEL_SCALE]
}
