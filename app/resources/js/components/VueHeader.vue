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
             :class="[isOpenHamburgerMenu ? 'max-md:max-h-52' : 'max-md:max-h-0']">
            <div v-if="isLoggedIn" class="navbar-menu">
                <div v-if="isIslandRegistered" class="navbar-item navbar-username">
                    {{ store.user.island.name }}島
                </div>
                <a v-if="isIslandRegistered" class="menu-item primary group" :href="'/islands/'+store.user.island.id+'/plans'"
                   title="開発画面に行く">
                    <font-awesome-icon
                        icon="fa-solid fa-pen-to-square"
                        class="menu-icon text-on-primary group-hover:text-primary"
                    ></font-awesome-icon>
                    <span class="menu-title plan-title text-on-primary group-hover:text-primary">島を開発</span>
                </a>
                <a v-if="isIslandRegistered" class="menu-item group" href="/settings" title="設定">
                    <font-awesome-icon
                        icon="fa-solid fa-gear"
                        class="menu-icon text-on-surface-variant group-hover:text-surface-variant"
                    ></font-awesome-icon>
                    <span class="menu-title text-on-surface-variant group-hover:text-surface-variant">設定</span>
                </a>
                <a v-if="!isIslandRegistered" class="button-primary navbar-register" href="/register">
                    島を探しに行く（新規登録）
                </a>
                <form method="POST" name="logout" action="/logout">
                    <input type="hidden" name="_token" :value="csrfToken">
                    <a href="javascript:logout.submit()"  class="menu-item group" title="ログアウト">
                        <font-awesome-icon
                            icon="fa-solid fa-arrow-right-from-bracket"
                            class="menu-icon text-on-surface-variant group-hover:text-surface-variant"
                        ></font-awesome-icon>
                        <span class="menu-title text-on-surface-variant group-hover:text-surface-variant">ログアウト</span>
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
import {library} from "@fortawesome/fontawesome-svg-core";
import {faPenToSquare} from "@fortawesome/free-solid-svg-icons";
import {faGear} from "@fortawesome/free-solid-svg-icons";
import {faArrowRightFromBracket} from "@fortawesome/free-solid-svg-icons";

export default defineComponent({
    components: {ThemeSwitcher},
    data() {
        return {
            isOpenHamburgerMenu: false,
        }
    },
    setup(props) {
        library.add(faPenToSquare, faGear, faArrowRightFromBracket);

        const store = useMainStore();
        const theme = localStorage.getItem('theme');
        if (theme !== null && theme !== undefined) {
            store.theme = JSON.parse(theme);
        }
        if(props.ownedIsland !== null && props.ownedIsland !== undefined) {
            store.user = {
                user_id: props.user.id,
                island: {
                    id: props.ownedIsland.id,
                    name: props.ownedIsland.name,
                    owner_name: props.ownedIsland.owner_name,
                }
            }
        }
        return {store};
    },
    mounted() {
        this.store.changeTheme(this.store.theme);
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
    @apply min-w-full mb-3 pt-2 pb-2 md:px-5 flex max-md:flex-wrap bg-surface text-on-surface drop-shadow-md items-center;

    .navbar-left {
        @apply w-fit inline-flex mx-3 items-center;

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
        @apply block h-full;
        // sp
        @apply max-md:mt-3 max-md:w-full;
        // desktop
        @apply md:flex md:items-center md:ml-auto;

        .navbar-register {
            @apply text-center;
            // sp
            @apply max-md:w-full max-md:rounded-none max-md:mb-2 max-md:drop-shadow-none max-md:mt-4;
            // desktop
            @apply md:inline-block md:mr-3;
        }

        .navbar-username {
            @apply text-center leading-none;
            // sp
            @apply max-md:mb-2 max-md:pb-1 max-md:border-b-2 max-md:font-bold;
            // desktop
            @apply md:max-w-xs md:mr-2 md:pr-2 md:border-r-2;
        }

        .menu-item {
            @apply hover:bg-on-surface-variant;
            // sp
            @apply flex items-center px-8 py-2 max-md:mb-1;
            // desktop
            @apply md:mx-1 md:p-2 md:rounded-full bg-surface-variant;

            &.primary {
                @apply bg-primary hover:bg-primary-container;
            }

            .menu-icon {
                @apply inline w-5 h-5 max-w-[1.25rem] max-h-[1.25rem];
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
