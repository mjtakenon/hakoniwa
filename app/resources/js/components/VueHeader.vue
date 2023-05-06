<template>
    <div>
        <nav class="navbar" role="navigation" aria-label="main navigation">
            <div class="navbar-brand">
                <a class="navbar-item" href="/">
                    🏝️
                </a>

                <button
                    @click="isOpenHamburgerMenu=!isOpenHamburgerMenu" role="button" :class="[{'is-active':isOpenHamburgerMenu}, 'navbar-burger']" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </button>
            </div>

            <div id="navbarBasicExample" class="navbar-menu">
                <div class="navbar-start">
                    <a class="navbar-item" href="/">
                        Home
                    </a>
                </div>

                <div v-if="isLoggedIn" class="navbar-end">
                    <div class="navbar-item">
                        {{ user.name }}
                    </div>
                    <div v-if="isIslandRegistered" class="navbar-item">
                        <a class="button is-primary" :href="'/islands/'+ownedIsland.id+'/plans'" > <!--fixme:island_id-->
                            開発画面に行く
                        </a>
                    </div>
                    <div v-else class="navbar-item">
                        <a class="button is-primary" href="/register">
                            島を探しに行く（新規登録）
                        </a>
                    </div>
                    <div class="navbar-item">
                        <form method="POST" name="logout" action="/logout">
                            <input type="hidden" name="_token" :value="csrfToken">
                            <a class="button" href="javascript:logout.submit()">
                                ログアウト
                            </a>
                        </form>
                    </div>
                </div>
                <div v-else>
                    <a href="/auth/google/redirect">
                        <img :src="'/img/btn_google_signin_light_normal_web.png'">
                    </a>
                    <a href="/auth/yahoo/redirect">
                        <img :src="'/img/yahoo_japan_icon_64.png'">
                        ログイン
                    </a>
                </div>
            </div>
        </nav>

        <nav v-show="isOpenHamburgerMenu" class="navbar" role="navigation" aria-label="dropdown navigation">
            <div class="navbar-item has-dropdown">
                <div class="navbar-dropdown">
                    <div v-if="isLoggedIn" class="navbar-end">
                        <div v-if="isIslandRegistered" class="navbar-item">
                            <a :href="'/islands/'+ownedIsland.id+'/plans'" > <!--fixme:island_id-->
                                開発画面に行く
                            </a>
                        </div>
                        <div v-else class="navbar-item">
                            <a href="/register">
                                島を探しに行く（新規登録）
                            </a>
                        </div>
                        <div class="navbar-item">
                            <a href="javascript:logout.submit()">
                                ログアウト
                            </a>
                        </div>
                    </div>
                    <div v-else>
                        <a href="/auth/google/redirect">
                            <img :src="'/img/btn_google_signin_light_normal_web.png'">
                        </a>
                    </div>
                    <hr/>
                </div>
            </div>
        </nav>
    </div>
</template>

<script lang="ts">
export default {
    data() {
        return {
            isOpenHamburgerMenu: false,
        }
    },
    setup() {
    },
    methods: {
    },
    mounted() {
        // console.log(this.$props)
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
};
</script>

<style lang="scss" scoped>
</style>
