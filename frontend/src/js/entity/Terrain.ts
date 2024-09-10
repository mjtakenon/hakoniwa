import { Cell } from './Cell.js'
import { Edge } from './Edge.js'

export interface Terrain {
  cells: Cell[]
  edges: Edge[]
}
