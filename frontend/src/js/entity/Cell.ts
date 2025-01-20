import {Point} from './Point.js'

export interface Cell {
  type: CellType
  data: {
    info: string
    point: Point
    elevation: number
    sub_type?: string
  }
}

export const CELL_SIZE_X = Math.sqrt(3)
export const CELL_SIZE_Y = 1.5
export const DEFAULT_MODEL_SCALE = 1

export const CELL_MODELS = {
  city: {default: [{model: 'plain'}, {model: 'village'}]},
  factory: {default: [{model: 'plain'}]},
  farm: {default: [{model: 'plain'}, {model: 'farm'}]},
  farm_dome: {default: [{model: 'plain'}]},
  forest: {default: [{model: 'plain'}, {model: 'trees'}]},
  metropolis: {default: [{model: 'plain'}, {model: 'village'}]},
  mountain: {default: [{model: 'mountain'}]},
  volcano: {default: [{model: 'volcano'}]},
  mine: {default: [{model: 'wasteland'}]},
  oilfield: {default: [{model: 'shallow'}]},
  plain: {default: [{model: 'plain'}]},
  sea: {default: [{model: 'sea', elevation_multiply: 0}, {model: 'floor'}]},
  shallow: {default: [{model: 'shallow', elevation_multiply: 0}, {model: 'floor'}]},
  lake: {default: [{model: 'shallow', elevation_multiply: 0}, {model: 'floor'}]},
  large_factory: {default: [{model: 'plain'}]},
  town: {default: [{model: 'plain'}, {model: 'village'}]},
  village: {default: [{model: 'plain'}, {model: 'village'}]},
  wasteland: {default: [{model: 'wasteland'}]},
  missile_base: {default: [{model: 'plain'}]},
  seabed_base: {default: [{model: 'sea'}]},
  park: {default: [{model: 'plain'}]},
  monument_of_agriculture: {default: [{model: 'plain'}]},
  monument_of_mining: {default: [{model: 'plain'}]},
  monument_of_master: {default: [{model: 'plain'}]},
  monument_of_peace: {default: [{model: 'plain'}]},
  monument_of_war: {default: [{model: 'plain'}]},
  monument_of_conquest: {default: [{model: 'plain'}]},
  inora: {default: [{model: 'plain'}]},
  red_inora: {default: [{model: 'plain'}]},
  dark_inora: {default: [{model: 'plain'}]},
  king_inora: {default: [{model: 'plain'}]},
  sanjira: {
    default: [{model: 'plain'}],
    metalized: [{model: 'plain'}]
  },
  kujira: {
    default: [{model: 'plain'}],
    metalized: [{model: 'plain'}]
  },
  hamunemu: {default: [{model: 'plain'}]},
  ghost_inora: {default: [{model: 'plain'}]},
  slime: {default: [{model: 'plain'}]},
  slime_legend: {default: [{model: 'plain'}]},
  levinoth: {
    default: [{model: 'plain'}],
    shallow: [{model: 'plain'}],
    sea: [{model: 'plain'}]
  },
  begenoth: {default: [{model: 'plain'}]},
  egg: {default: [{model: 'plain'}]},
  transport_ship: {
    default: [{model: 'sea'}],
    shallow: [{model: 'shallow'}],
    sea: [{model: 'sea'}]
  },
  battleship: {
    default: [{model: 'sea'}],
    shallow: [{model: 'shallow'}],
    sea: [{model: 'sea'}]
  },
  submarine: {
    default: [{model: 'sea'}],
    shallow: [{model: 'shallow'}],
    sea: [{model: 'sea'}]
  },
  pirate: {
    default: [{model: 'sea'}],
    shallow: [{model: 'shallow'}],
    sea: [{model: 'sea'}]
  },
  levinoth_battleship: {
    default: [{model: 'sea'}],
    shallow: [{model: 'shallow'}],
    sea: [{model: 'sea'}]
  },
  levinoth_submarine: {
    default: [{model: 'sea'}],
    shallow: [{model: 'shallow'}],
    sea: [{model: 'sea'}]
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
  return Object.keys(CELL_MODELS)
}

export const getCellSubTypes = (type: CellType) => {
  return Object.keys(CELL_MODELS[type])
}

export const getCellModels = (type: CellType, subType: string | null = null) => {
  return subType ? CELL_MODELS[type][subType] : CELL_MODELS[type].default
}

export const getRotation = () => {
  return [0, 0, 0]
}

export const getScale = () => {
  return [DEFAULT_MODEL_SCALE, DEFAULT_MODEL_SCALE, DEFAULT_MODEL_SCALE]
}
