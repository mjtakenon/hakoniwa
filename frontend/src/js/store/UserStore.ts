import { defineStore } from 'pinia'
import { AjaxResult, ErrorType, RequestStatus } from '$entity/Network.js'
import axios from 'axios'
import { computed, ref } from 'vue'
import { User } from '$entity/User.js'

export const useUserStore = defineStore('user', () => {
  const user = ref<User | null>(null)

  const isLoggedIn = computed(() => {
    return user.value !== null
  })

  const hasIsland = computed(() => {
    return isLoggedIn.value && user.value.island !== null
  })

  const patchIslandName = async (name: string, owner: string): Promise<AjaxResult> => {
    let result = {} as AjaxResult
    console.debug('PATCH', '/api/islands/' + user.value.island.id)
    await axios
      .patch('/api/islands/' + user.value.island.id, {
        name: name,
        owner_name: owner
      })
      .then((res) => {
        result.status = RequestStatus.Success
        user.value.island.name = res.data.island.name
        user.value.island.owner_name = res.data.island.owner_name
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
  return { user, isLoggedIn, hasIsland, patchIslandName }
})
