<template>
    <div id="theme-switch">
        <div class="switch-bar" @click="onClickThemeToggle">
            <div class="switch-button" :class="{'active' : isDark}">
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import {Theme} from "../store/Entity/Theme";
import {useMainStore} from "../store/MainStore";

export default defineComponent({
    data() {
        return {
            theme: [
                { name: 'light', themeClass: 'theme-light', type: 'light' },
                { name: 'dark', themeClass: 'theme-dark', type: 'dark' }
            ] as Theme[],
            appElement: document.getElementById("app"),
            isDark: false
        }
    },
    setup() {
        const store = useMainStore();
        const theme = localStorage.getItem('theme');
        if (theme !== undefined) {
            store.theme = JSON.parse(theme);
        }
        return { store };
    },
    mounted() {
        this.isDark = (this.store.theme.type === 'dark');
        this.changeTheme(this.store.theme);
    },
    methods: {
        changeTheme(theme: Theme) {
            console.debug(theme);
            this.appElement.classList.remove(...this.appElement.classList);
            this.appElement.classList.add(theme.themeClass);
            this.appElement.classList.add(theme.type.toString());
            localStorage.setItem('theme', JSON.stringify(theme));
        },
        onClickThemeToggle() {
            this.isDark = !this.isDark;
            if(this.isDark) {
                this.store.theme = this.theme[1];
            }
            else {
                this.store.theme = this.theme[0];
            }
            this.changeTheme(this.store.theme);
        }
    }
})

</script>

<style scoped lang="scss">
#theme-switch {
    .switch-bar {
        @apply w-10 h-5 my-2 rounded-3xl relative cursor-pointer;
        @apply bg-surface-variant;
        @apply transition-all duration-300 ease-in-out;
        // sp
        @apply mx-auto;
        // desktop
        @apply md:mx-2;

        .switch-button {
            @apply w-5 h-5 rounded-3xl absolute top-0 bottom-0 left-0 m-auto;
            @apply bg-on-surface-variant;
        }
        .switch-button.active {
            @apply left-5;
        }
    }
}

</style>
