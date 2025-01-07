import {Point} from './Point.js'
import {CELL_PATHS, CellType, CELL_SIZE_X, DEFAULT_MODEL_SCALE, CELL_SIZE_Y} from "$entity/Cell.js";

export interface Edge {
  type: EdgeType
  data: {
    point: Point
    face: number
    sub_type?: string
  }
}

export const EDGE_WIDTH_X = 0.2
export const EDGE_WIDTH_Y = EDGE_WIDTH_X * 0.5 * Math.sqrt(3) // 直角の長辺を0.2とする30°の傾きなので三平方の定理よりX*0.5*sqrt(3)

export const EDGE_PATHS = {
  wasteland: { default: [{ path: '/img/hakoniwa/glb/wasteland_edge.glb' }] },
  plain: { default: [{ path: '/img/hakoniwa/glb/plain_edge.glb' }] },
  sea: { default: [{ path: '/img/hakoniwa/glb/sea_edge.glb', opacity: 0.8 }] },
  shallow: { default: [{ path: '/img/hakoniwa/glb/shallow_edge.glb', opacity: 0.8 }] },
} as const

export type EdgeType =
  | 'wasteland'
  | 'plain'
  | 'sea'
  | 'shallow'

export const getEdgeTypes = () => {
  return Object.keys(EDGE_PATHS)
}

export const getEdgeSubTypes = (type: EdgeType) => {
  return Object.keys(EDGE_PATHS[type])
}

export const getEdgePath = (type: EdgeType, subType: string | null = null) => {
  return subType ? EDGE_PATHS[type][subType] : EDGE_PATHS[type].default
}

export const getPosition = (edge: Edge, position: Array<number>) => {
  // face はcellの 0: 左上, 1: 右上, 2: 左, 3: 右, 4: 左下, 5: 右下。基本0, 1, 2のみ
  const QUARTER_SIZE_X = ((CELL_SIZE_X + EDGE_WIDTH_X) / 4) * DEFAULT_MODEL_SCALE
  const HALF_SIZE_Y = ((CELL_SIZE_Y + EDGE_WIDTH_Y) / 2) * DEFAULT_MODEL_SCALE

  switch (edge.data.face) {
    case 0:
      position[0] -= QUARTER_SIZE_X
      position[2] -= HALF_SIZE_Y
      return position;
    case 1:
      position[0] += QUARTER_SIZE_X
      position[2] -= HALF_SIZE_Y
      return position;
    case 2:
      position[0] -= QUARTER_SIZE_X * 2
      return position;
    case 3:
      position[0] += QUARTER_SIZE_X * 2
      return position;
    case 4:
      position[0] -= QUARTER_SIZE_X
      position[2] += HALF_SIZE_Y
      return position;
    case 5:
      position[0] += QUARTER_SIZE_X
      position[2] += HALF_SIZE_Y
      return position;
    default:
      return position;
  }
}

export const getRotation = (edge: Edge) => {
  switch (edge.data.face) {
    case 0:
    case 5:
      return [0, Math.PI/3*2, 0]
    case 1:
    case 4:
      return [0, Math.PI/3, 0]
    case 2:
    case 3:
      return [0, 0, 0]
    default:
      return [0, 0, 0]
  }
}

export const getScale = (edge: Edge) => {
  return [DEFAULT_MODEL_SCALE, DEFAULT_MODEL_SCALE, DEFAULT_MODEL_SCALE]
}
