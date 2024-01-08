import { defineStore } from 'pinia'
import { Status } from './Entity/Status.js'
import { Hakoniwa } from './Entity/Hakoniwa.js'
import { Island } from './Entity/Island.js'
import { Terrain } from './Entity/Terrain.js'
import { Log } from './Entity/Log.js'
import { Plan } from './Entity/Plan.js'
import axios from 'axios'
import { Point } from './Entity/Point.js'
import { Turn } from './Entity/Turn.js'
import { AjaxResult, ErrorType, RequestStatus } from './Entity/Network.js'
import { Achievement } from './Entity/Achievement.js'
import { BbsMessage, BbsVisibility } from './Entity/Bbs.js'

export interface PiniaState {
  user: {
    user_id: number
    island: Island
  }
  bbs: BbsMessage[]
}

export const useBbsStore = defineStore('bbs', {
  state: (): PiniaState => {
    return {
      user: {
        user_id: 0,
        island: null
      },
      bbs: []
    }
  },
  getters: {},
  actions: {
    async postBbs(comment: string, visibility: BbsVisibility, island: Island): Promise<AjaxResult> {
      let result = {} as AjaxResult
      console.debug('POST', '/api/islands/' + island.id + '/bbs')
      await axios
        .post('/api/islands/' + island.id + '/bbs', {
          comment: comment,
          visibility: visibility
        })
        .then((res) => {
          result.status = RequestStatus.Success
          this.bbs = res.data.bbs
        })
        .catch((err) => {
          console.debug(err)
          result.status = RequestStatus.Failed
          const code = err.response.data.code
          if (code === 'lack_of_funds') {
            result.error = ErrorType.LackOfFunds
          } else if (err.response.status === ErrorType.TooManyRequests) {
            // TODO:enumに含まれているかでstatusを直で入れるようにしたい
            result.error = ErrorType.TooManyRequests
          } else if (err.response.status === ErrorType.NotFound) {
            result.error = ErrorType.NotFound
          } else {
            result.error = ErrorType.Unknown
          }
        })

      return result
    },
    async deleteBbs(target: BbsMessage, island: Island): Promise<AjaxResult> {
      let result = {} as AjaxResult
      console.debug('DELETE', '/api/islands/' + island.id + '/bbs/' + target.id)

      await axios
        .delete('/api/islands/' + island.id + '/bbs/' + target.id)
        .then((res) => {
          result.status = RequestStatus.Success
          this.bbs = res.data.bbs
        })
        .catch((err) => {
          console.debug(err)
          result.status = RequestStatus.Failed
        })
      return result
    }
  }
})
