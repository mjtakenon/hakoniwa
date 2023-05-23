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
             :class="[isOpenHamburgerMenu ? 'max-md:max-h-48' : 'max-md:max-h-0']">
            <div v-if="isLoggedIn" class="navbar-menu">
                <div v-if="isIslandRegistered" class="navbar-item navbar-username">
                    {{ ownedIsland.name }}島
                </div>
                <a v-if="isIslandRegistered" class="menu-item primary group" :href="'/islands/'+ownedIsland.id+'/plans'" title="開発画面に行く">
                    <svg class="menu-icon fill-on-primary group-hover:fill-primary" xmlns="http://www.w3.org/2000/svg" viewBox="0 96 960 960"><path d="M180 976q-24 0-42-18t-18-42V296q0-24 18-42t42-18h65v-60h65v60h340v-60h65v60h65q24 0 42 18t18 42v301h-60V486H180v430h319v60H180Zm709-219-71-71 29-29q8-8 21-8t21 8l29 29q8 8 8 21t-8 21l-29 29Zm-330 259v-71l216-216 71 71-216 216h-71Z"/></svg>
                    <span class="menu-title primary">開発画面に行く</span>
                </a>
                <a v-if="isIslandRegistered" class="menu-item group" href="/settings" title="設定">
                    <svg class="menu-icon fill-on-surface-variant group-hover:fill-surface-variant" xmlns="http://www.w3.org/2000/svg" viewBox="0 96 960 960"><path d="m388 976-20-126q-19-7-40-19t-37-25l-118 54-93-164 108-79q-2-9-2.5-20.5T185 576q0-9 .5-20.5T188 535L80 456l93-164 118 54q16-13 37-25t40-18l20-127h184l20 126q19 7 40.5 18.5T669 346l118-54 93 164-108 77q2 10 2.5 21.5t.5 21.5q0 10-.5 21t-2.5 21l108 78-93 164-118-54q-16 13-36.5 25.5T592 850l-20 126H388Zm92-270q54 0 92-38t38-92q0-54-38-92t-92-38q-54 0-92 38t-38 92q0 54 38 92t92 38Z"/></svg>
                    <span class="menu-title">設定</span>
                </a>
                <a v-if="!isIslandRegistered" class=" button-primary navbar-register" href="/register">
                    島を探しに行く（新規登録）
                </a>
                <form class="menu-item group" method="POST" name="logout" action="/logout">
                    <input type="hidden" name="_token" :value="csrfToken">
                    <a href="javascript:logout.submit()" title="ログアウト">
                        <svg class="menu-icon fill-on-surface-variant group-hover:fill-surface-variant" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M180-120q-24 0-42-18t-18-42v-600q0-24 18-42t42-18h291v60H180v600h291v60H180Zm486-185-43-43 102-102H375v-60h348L621-612l43-43 176 176-174 174Z"/></svg>
                        <span class="menu-title">ログアウト</span>
                    </a>
                </form>
            </div>
            <div v-else class="navbar-menu">
                <a class="block max-md:mb-2 md:mr-2" href="/auth/google/redirect">
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
import {useMainStore} from "../store/MainStore";
import {defaultTheme} from "../store/Entity/Theme";

export default defineComponent({
    components: {ThemeSwitcher},
    data() {
        return {
            isOpenHamburgerMenu: false,
        }
    },
    setup() {
        const store = useMainStore();
        const theme = localStorage.getItem('theme');
        if (store.theme === undefined) {
            if (theme !== undefined) {
                store.theme = JSON.parse(theme);
            } else {
                store.theme = defaultTheme;
            }
        }
        return {store};
    },
    mounted() {
        this.store.changeTheme(this.store.theme);
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
        @apply block h-full max-md:mt-3 max-md:w-full md:flex md:items-center md:ml-auto;

        .navbar-register {
            @apply text-center;
            // sp
            @apply max-md:w-full max-md:rounded-none max-md:mb-2 max-md:drop-shadow-none max-md:mt-4;
            // desktop
            @apply md:inline-block md:mr-3;
        }

        .navbar-username {
            @apply text-center;
            // sp
            @apply max-md:mb-2 max-md:pb-1 max-md:border-b-2 max-md:font-bold;
            // desktop
            @apply md:mr-2 md:pr-2 md:border-r-2;
        }

        .menu-item {
            @apply hover:bg-on-surface-variant;
            // sp
            @apply flex items-center px-8 py-2 max-md:mb-1;
            // desktop
            @apply md:mr-2 md:p-1.5 md:rounded-full bg-surface-variant;

            &.primary{
                @apply bg-primary hover:bg-primary-container;
            }

            .menu-icon {
                @apply inline w-6 h-6;
                // sp
                @apply max-md:mr-5;
            }

            .menu-title {
                // sp
                @apply text-on-surface-variant font-bold;
                // desktop
                @apply md:hidden;

                &.primary {
                    @apply text-on-primary;
                }
            }
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
    }
}
</style>
