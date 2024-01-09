import { defineStore } from 'pinia'
import { Island } from './Entity/Island.js'
import axios from 'axios'
import { AjaxResult, ErrorType, RequestStatus } from './Entity/Network.js'
import { BbsMessage, BbsVisibility } from './Entity/Bbs.js'
import { ref } from 'vue'

export const useBbsStore = defineStore('bbs', () => {
  const bbs = ref<BbsMessage[]>([])

  const postBbs = async (comment: string, visibility: BbsVisibility, island: Island): Promise<AjaxResult> => {
    let result = {} as AjaxResult
    console.debug('POST', '/api/islands/' + island.id + '/bbs')
    await axios
      .post('/api/islands/' + island.id + '/bbs', {
        comment: comment,
        visibility: visibility
      })
      .then((res) => {
        result.status = RequestStatus.Success
        bbs.value = res.data.bbs
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
  }
  const deleteBbs = async (target: BbsMessage, island: Island): Promise<AjaxResult> => {
    let result = {} as AjaxResult
    console.debug('DELETE', '/api/islands/' + island.id + '/bbs/' + target.id)

    await axios
      .delete('/api/islands/' + island.id + '/bbs/' + target.id)
      .then((res) => {
        result.status = RequestStatus.Success
        bbs.value = res.data.bbs
      })
      .catch((err) => {
        console.debug(err)
        result.status = RequestStatus.Failed
      })
    return result
  }

  return { bbs, postBbs, deleteBbs }
})
