<template>
  <nav class="navbar">
    <a class="navbar-left text-on-surface" href="/">
      <div class="navbar-brand">
        <img :src="'/favicon.ico'" />
      </div>
      <div class="navbar-title">やまにてぃ（仮）</div>
    </a>
    <button id="hamburger-button" @click="isOpenHamburgerMenu = !isOpenHamburgerMenu">
      <img class="hamburger-icon" :src="'/img/hakoniwa/ui/hamburger.svg'" />
    </button>

    <div class="hamburger-elements" :class="[isOpenHamburgerMenu ? 'max-md:max-h-52' : 'max-md:max-h-0']">
      <div v-if="isLoggedIn" class="navbar-menu">
        <div v-if="isIslandRegistered" class="navbar-item navbar-username">{{ store.user.island.name }}島</div>
        <a
          v-if="isIslandRegistered"
          class="menu-item primary group"
          :href="'/islands/' + store.user.island.id + '/plans'"
          title="開発画面に行く">
          <FontAwesomeIcon
            icon="fa-solid fa-pen-to-square"
            class="menu-icon text-on-primary group-hover:text-primary"></FontAwesomeIcon>
          <span class="menu-title plan-title text-on-primary group-hover:text-primary">島を開発</span>
        </a>
        <a v-if="isIslandRegistered" class="menu-item group" href="/settings" title="設定">
          <FontAwesomeIcon
            icon="fa-solid fa-gear"
            class="menu-icon text-on-surface-variant group-hover:text-surface-variant"></FontAwesomeIcon>
          <span class="menu-title text-on-surface-variant group-hover:text-surface-variant">設定</span>
        </a>
        <a v-if="!isIslandRegistered" class="button-primary navbar-register" href="/register">
          島を探しに行く（新規登録）
        </a>
        <form method="POST" name="logout" action="/logout">
          <input type="hidden" name="_token" :value="csrfToken" />
          <a href="javascript:logout.submit()" class="menu-item group" title="ログアウト">
            <FontAwesomeIcon
              icon="fa-solid fa-arrow-right-from-bracket"
              class="menu-icon text-on-surface-variant group-hover:text-surface-variant"></FontAwesomeIcon>
            <span class="menu-title text-on-surface-variant group-hover:text-surface-variant">ログアウト</span>
          </a>
        </form>
      </div>
      <div v-else class="navbar-menu">
        <a class="block max-md:mb-2 md:mr-2" href="/auth/google/redirect">
          <img class="mx-auto" :src="'/img/btn_google_signin_light_normal_web.png'" />
        </a>
        <div class="navbar-yahoo">
          <a class="yahoo-link-box" href="/auth/yahoo/redirect">
            <div class="yahoo-logo">
              <img class="yahoo-logo-img" :src="'/img/yahoo_japan_icon_64.png'" />
            </div>
            <span class="yahoo-text">ログイン</span>
          </a>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faArrowRightFromBracket, faGear, faPenToSquare } from '@fortawesome/free-solid-svg-icons'
import { Island } from '$js/entity//Island.js'
import { useUserSettingsStore } from '$store/UserSettingsStore.js'
import { useUserStore } from '$store/UserStore.js'

let isOpenHamburgerMenu = ref(false)

interface Props {
  csrfToken: string
  isLoggedIn: boolean
  isIslandRegistered: boolean
  userId?: number
  ownedIsland?: Island
}

const props = defineProps<Props>()

library.add(faPenToSquare, faGear, faArrowRightFromBracket)

const store = useUserStore()
const userSettings = useUserSettingsStore()
const theme = localStorage.getItem('theme')

if (theme !== null && theme !== undefined) {
  userSettings.theme = JSON.parse(theme)
}

store.$patch((state) => {
  state.user = props.userId
    ? {
        id: props.userId,
        island: props.ownedIsland ?? null
      }
    : null
})

onMounted(() => {
  userSettings.changeTheme(userSettings.theme)
})
</script>

<style lang="scss" scoped>
.navbar {
  @apply mb-3 flex min-w-full items-center bg-surface pb-2 pt-2 text-on-surface drop-shadow-md max-md:flex-wrap md:px-5;

  .navbar-left {
    @apply mx-3 inline-flex w-fit items-center;

    .navbar-brand {
      @apply mr-5 inline-block h-full dark:invert dark:filter;
    }

    .navbar-title {
      @apply text-lg font-bold max-md:hidden;
    }
  }

  #hamburger-button {
    @apply ml-auto mr-3 block h-7 w-7 border-none bg-none p-0 drop-shadow-none hover:bg-none md:hidden;

    .hamburger-icon {
      @apply dark:invert dark:filter;
    }
  }

  .hamburger-elements {
    @apply overflow-hidden transition-all duration-500 ease-in-out max-md:w-full md:ml-auto;
  }

  .navbar-menu {
    @apply block h-full;
    // sp
    @apply max-md:mt-3 max-md:w-full;
    // desktop
    @apply md:ml-auto md:flex md:items-center;

    .navbar-register {
      @apply text-center;
      // sp
      @apply max-md:mb-2 max-md:mt-4 max-md:w-full max-md:rounded-none max-md:drop-shadow-none;
      // desktop
      @apply md:mr-3 md:inline-block;
    }

    .navbar-username {
      @apply text-center leading-none;
      // sp
      @apply max-md:mb-2 max-md:border-b-2 max-md:pb-1 max-md:font-bold;
      // desktop
      @apply md:mr-2 md:max-w-xs md:border-r-2 md:pr-2;
    }

    .menu-item {
      @apply hover:bg-on-surface-variant;
      // sp
      @apply flex items-center px-8 py-2 max-md:mb-1;
      // desktop
      @apply bg-surface-variant md:mx-1 md:rounded-full md:p-2;

      &.primary {
        @apply bg-primary hover:bg-primary-container;
      }

      .menu-icon {
        @apply inline h-5 max-h-[1.25rem] w-5 max-w-[1.25rem];
        // sp
        @apply max-md:mr-5;
      }

      .menu-title {
        // sp
        @apply font-bold;
        // desktop
        @apply md:hidden;

        &.plan-title {
          @apply md:ml-2 md:mr-1 md:inline;
        }
      }
    }

    .navbar-yahoo {
      @apply mx-auto block w-fit rounded-md border-2 border-[#ff0033] bg-[#ff0033] drop-shadow md:inline-block;

      .yahoo-link-box {
        @apply flex items-center;

        .yahoo-logo {
          @apply rounded-l bg-white;
        }

        .yahoo-logo-img {
          @apply mx-[11px] my-[6px] w-8;
        }

        .yahoo-text {
          @apply px-7 font-bold text-white;
        }
      }
    }
  }
}
</style>
