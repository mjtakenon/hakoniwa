import { Island } from './Island.js'
import { Status } from './Status.js'

export interface User {
  id: number
  island?: Island
  status?: Status
}
