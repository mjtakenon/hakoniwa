<template>
    <div class="theme-settings">
        <h2 class="subtitle">テーマ設定</h2>
        <p class="descriptions">
            画面表示のテーマを設定できます。
        </p>
        <div class="theme-buttons">
            <div v-for="theme of themes" class="mr-2" :class="theme.themeClass">
                <button @click="onClickTheme(theme)">{{ theme.name }}</button>
            </div>
        </div>
        <div class="theme-samples">
            <h3 class="samples-title">サンプル</h3>
            <div class="samples-wrapper">
                <div class="samples-grid">
                    <div class="sample-box bg-background text-on-background">
                        background
                    </div>
                    <div class="sample-box bg-surface text-on-surface">
                        surface
                    </div>
                    <div class="sample-box bg-primary text-on-primary">
                        <div class="sample-box">primary</div>
                        <div class="sample-box bg-primary-container text-on-primary-container">
                            primary container
                        </div>
                    </div>
                    <div class="sample-box bg-secondary text-on-secondary">
                        <div class="sample-box">secondary</div>
                        <div class="sample-box bg-secondary-container text-on-secondary-container">
                            secondary container
                        </div>
                    </div>
                    <div class="sample-box bg-alert text-on-alert">
                        <div class="sample-box">alert</div>
                        <div class="sample-box bg-alert-container text-on-alert-container">
                            alert container
                        </div>
                    </div>
                    <div class="sample-box bg-error text-on-error">
                        <div class="sample-box">error</div>
                        <div class="sample-box bg-error-container text-on-error-container">
                            error container
                        </div>
                    </div>
                    <div class="sample-box bg-surface-variant text-on-surface-variant">
                        surface-variant
                    </div>
                    <div class="sample-box border-2">
                        outline
                    </div>
                    <div class="sample-box font-bold text-on-link">
                        Link Text Color
                    </div>
                    <div class="sample-box font-bold">
                        <div class="sample-box">
                            <span class="mr-3">PLUS</span>
                            <span class="text-on-plus border-b-2 border-b-plus">+ 100 pts</span>
                        </div>
                        <div class="sample-box">
                            <span class="mr-3">MINUS</span>
                            <span class="text-on-minus border-b-2 border-b-minus">- 100 pts</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent} from 'vue'
import themes from '../../../ThemeList.json'
import {Theme} from "../../../store/Entity/Theme";
import {useMainStore} from "../../../store/MainStore";

export default defineComponent({
    data() {
        return {
            themes: themes as Theme[]
        }
    },
    setup() {
        const store = useMainStore();
        return {store}
    },
    methods: {
        onClickTheme(theme: Theme) {
            if (this.store.theme.name === theme.name) return;
            this.store.changeTheme(theme);
        }
    }
})
</script>

<style scoped lang="scss">
.theme-settings {
    // general
    @apply w-full bg-surface-variant rounded-md;
    // sp
    @apply p-2;
    // desktop
    @apply md:py-4 md:px-6;

    .subtitle {
        @apply my-2 border-l-4 border-on-background pl-6 font-bold;
    }

    .descriptions {
        @apply mt-4 mx-2 md:mx-6;
    }

    .theme-buttons {
        @apply flex justify-center;
    }

    .theme-samples {
        @apply mt-6;
        // sp
        @apply px-2;
        // desktop
        @apply md:px-10;

        .samples-title {
            @apply mb-4 text-on-surface-variant border-b border-dashed border-on-surface-variant;
        }

        .samples-wrapper {
            @apply w-full bg-background p-2;

            .samples-grid {
                @apply grid gap-2;
                // sp
                @apply grid-cols-1;
                // desktop
                @apply md:grid-cols-2;
            }

            .sample-box {
                @apply w-full min-h-[4rem] flex flex-wrap items-center justify-center rounded-md p-3;
            }
        }
    }
}
</style>
