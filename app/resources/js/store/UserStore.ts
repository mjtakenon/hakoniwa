import { defineStore } from 'pinia'
import { Island } from './Entity/Island.js'
import { AjaxResult, ErrorType, RequestStatus } from './Entity/Network.js'
import axios from 'axios'
import { Status } from './Entity/Status.js'

export interface PiniaState {
  user?: {
    id: number
    island?: Island
    status?: Status
  }
}

export const useUserStore = defineStore('user', {
  state: (): PiniaState => {
    return {
      user: null
    }
  },
  getters: {},
  actions: {
    async patchIslandName(name: string, owner: string): Promise<AjaxResult> {
      let result = {} as AjaxResult
      console.debug('PATCH', '/api/islands/' + this.user.island.id)
      await axios
        .patch('/api/islands/' + this.user.island.id, {
          name: name,
          owner_name: owner
        })
        .then((res) => {
          result.status = RequestStatus.Success
          this.user.island.name = res.data.island.name
          this.user.island.owner_name = res.data.island.owner_name
        })
        .catch((err) => {
          console.debug(err)
          const code = err.response.data.code
          if (code === 'lack_of_funds') {
            result.error = ErrorType.LackOfFunds
          } else if (code === 'not_changed') {
            result.error = ErrorType.NotChanged
          } else if (code === 'island_name_duplicated') {
            result.error = ErrorType.DuplicatedIslandName
          } else if (code === 'owner_name_duplicated') {
            result.error = ErrorType.DuplicatedOwnerName
          } else {
            result.error = ErrorType.Unknown
          }
        })
      return result
    }
  }
})
