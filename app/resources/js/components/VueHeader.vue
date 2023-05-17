<template>
    <nav class="navbar">
        <a class="navbar-left text-on-surface" href="/">
            <div class="navbar-brand">
                <img src="/favicon.ico">
            </div>
            <div class="navbar-title">
                やまにてぃ（仮）
            </div>
        </a>
        <button id="hamburger-button" @click="isOpenHamburgerMenu=!isOpenHamburgerMenu">
            <img class="hamburger-icon" src="/img/hakoniwa/ui/hamburger.svg">
        </button>

        <div class="hamburger-elements"
             :class="[isOpenHamburgerMenu ? 'max-md:max-h-36' : 'max-md:max-h-0']">
            <div v-if="isLoggedIn" class="navbar-menu">
                <div class="navbar-item navbar-username">
                    {{ user.name }}
                </div>
                <theme-switcher></theme-switcher>
                <a v-if="isIslandRegistered" class="navbar-item button-primary"
                   :href="'/islands/'+ownedIsland.id+'/plans'"> <!--fixme:island_id-->
                    開発画面に行く
                </a>
                <a v-else class="navbar-item button-primary" href="/register">
                    島を探しに行く（新規登録）
                </a>
                <div class="navbar-item">
                    <form method="POST" name="logout" action="/logout">
                        <input type="hidden" name="_token" :value="csrfToken">
                        <a href="javascript:logout.submit()">
                            <button>
                                ログアウト
                            </button>
                        </a>
                    </form>
                </div>
            </div>
            <div v-else class="navbar-menu">
                <a class="navbar-item" href="/auth/google/redirect">
                    <img class="mx-auto" src="/img/btn_google_signin_light_normal_web.png">
                </a>
                <div class="navbar-yahoo">
                    <a class="yahoo-link-box" href="/auth/yahoo/redirect">
                        <div class="yahoo-logo">
                            <img class="yahoo-logo-img" :src="'/img/yahoo_japan_icon_64.png'">
                        </div>
                        <span class="yahoo-text">ログイン</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import ThemeSwitcher from "./ThemeSwitcher.vue";

export default defineComponent({
    components: {ThemeSwitcher},
    data() {
        return {
            isOpenHamburgerMenu: false,
        }
    },
    setup() {
        // TODO: どうにかして設定したカラースキームを取得する
        document.getElementById("app").classList.add("theme-light");
        document.getElementById("app").classList.add("light");
        // document.getElementById("app").classList.add("theme-dark");
        // document.getElementById("app").classList.add("dark");
    },
    methods: {
    },
    mounted() {

    },
    computed: {
    },
    props: [
        'csrfToken',
        'isLoggedIn',
        'user',
        'isIslandRegistered',
        'ownedIsland',
    ]
});
</script>

<style lang="scss" scoped>

.navbar {
    @apply min-w-full mb-3 pt-2 pb-2 md:px-5 flex flex-wrap bg-surface text-on-surface drop-shadow-md items-center;

    .navbar-left {
        @apply inline-flex mx-3 items-center;

        .navbar-brand {
            @apply inline-block mr-5 h-full dark:filter dark:invert;
        }

        .navbar-title {
            @apply max-md:hidden text-lg font-bold;
        }
    }

    #hamburger-button {
        @apply ml-auto mr-3 block w-7 h-7 md:hidden bg-none hover:bg-none border-none drop-shadow-none p-0;

        .hamburger-icon {
            @apply dark:filter dark:invert;
        }
    }

    .hamburger-elements {
        @apply md:ml-auto max-md:w-full transition-all duration-500 ease-in-out overflow-hidden;
    }

    .navbar-menu {
        @apply block max-md:mt-3 max-md:w-full md:flex md:items-center md:ml-auto;

        .navbar-item {
            @apply block max-md:w-1/2 text-center mx-auto max-md:mb-2 md:inline-block md:mr-3;
        }

        .navbar-yahoo {
            @apply block md:inline-block mx-auto w-fit bg-[#ff0033] rounded-md border-2 border-[#ff0033] drop-shadow;

            .yahoo-link-box {
                @apply flex items-center;

                .yahoo-logo {
                    @apply bg-white rounded-l;
                }

                .yahoo-logo-img {
                    @apply w-8 mx-[11px] my-[6px];
                }

                .yahoo-text {
                    @apply font-bold text-white px-7;
                }
            }

        }

        .navbar-username {
            @apply text-center;
        }
    }
}
</style>
