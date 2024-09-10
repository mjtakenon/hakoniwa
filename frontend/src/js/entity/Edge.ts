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

export const EDGE_WIDTH_X = 2
export const EDGE_WIDTH_Y = 3

export const EDGE_PATHS = {
  wasteland: { horizontal: [{ path: '/img/hakoniwa/hexa/gltf/edge/river_h.gltf' }], vertical: [{ path: '/img/hakoniwa/hexa/gltf/edge/river_v.gltf' }] },
  plain: { horizontal: [{ path: '/img/hakoniwa/hexa/gltf/edge/river_h.gltf' }], vertical: [{ path: '/img/hakoniwa/hexa/gltf/edge/river_v.gltf' }] },
  sea: { horizontal: [{ path: '/img/hakoniwa/hexa/gltf/edge/river_h.gltf' }], vertical: [{ path: '/img/hakoniwa/hexa/gltf/edge/river_v.gltf' }] },
  shallow: { horizontal: [{ path: '/img/hakoniwa/hexa/gltf/edge/river_h.gltf' }], vertical: [{ path: '/img/hakoniwa/hexa/gltf/edge/river_v.gltf' }] },
} as const

export type EdgeType =
  | 'wasteland'
  | 'plain'
  | 'sea'
  | 'shallow'

export type FaceType =
  | 'horizontal'
  | 'vertial'

export const getEdgeTypes = () => {
  return Object.keys(EDGE_PATHS)
}

export const getEdgeSubTypes = (type: EdgeType) => {
  return Object.keys(EDGE_PATHS[type])
}

export const getEdgePath = (type: EdgeType, subType: string | null = null) => {
  return subType ? EDGE_PATHS[type][subType] : EDGE_PATHS[type].horizontal
}

export const getFaceType = (edge: Edge) => {
  return (edge.data.face === 2 || edge.data.face === 3) ? 'vertical' : 'horizontal'
}

export const getPosition = (edge: Edge, position: Array<number>) => {
  switch (edge.data.face) {
    case 0:
      position[0] -= ((CELL_SIZE_X + EDGE_WIDTH_X) / 4) * DEFAULT_MODEL_SCALE
      position[2] -= ((CELL_SIZE_Y - EDGE_WIDTH_Y) / 2 - 1) * DEFAULT_MODEL_SCALE
      return position;
    case 1:
      position[0] += ((CELL_SIZE_X + EDGE_WIDTH_X) / 4) * DEFAULT_MODEL_SCALE
      position[2] -= ((CELL_SIZE_Y - EDGE_WIDTH_Y) / 2 - 1) * DEFAULT_MODEL_SCALE
      return position;
    case 2:
      position[0] -= ((CELL_SIZE_X + EDGE_WIDTH_X) / 2) * DEFAULT_MODEL_SCALE
      return position;
    default:
      return position;
  }
}

export const getRotation = (edge: Edge) => {
  switch (edge.data.face) {
    case 0:
      return [0, Math.PI/2, 0]
    case 1:
      return [0, Math.PI/2, 0]
    case 2:
    case 3:
      return [0, Math.PI/2, 0]
    default:
      return 0
  }
}

export const getScale = (edge: Edge) => {

  switch (edge.data.face) {
    case 0:
      return [DEFAULT_MODEL_SCALE, DEFAULT_MODEL_SCALE, DEFAULT_MODEL_SCALE * -1]
    case 1:
      return [DEFAULT_MODEL_SCALE, DEFAULT_MODEL_SCALE, DEFAULT_MODEL_SCALE]
    case 2:
    case 3:
      return [DEFAULT_MODEL_SCALE, DEFAULT_MODEL_SCALE, DEFAULT_MODEL_SCALE]
    default:
      return [DEFAULT_MODEL_SCALE, DEFAULT_MODEL_SCALE, DEFAULT_MODEL_SCALE]
  }
}
