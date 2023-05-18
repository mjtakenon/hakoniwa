<template>
    <div class="stats">
        <div
            v-for="(status, index) in statuses"
            class="stats-box"
            :class="{'max-md:col-span-3': (index === 0)}"
            :key="status.title"
        >
            <div class="stats-box-title">
                {{ status.title }}
            </div>
            <div class="stats-box-data">
                <div
                    class="stats-box-num-wrapper"
                    :style="{maxWidth: status.maxWidth + 'px'}"
                    ref="dataNumRef"
                >
                    <div
                        class="stats-box-data-num"
                        :style="{fontSize: status.fontSize + 'px'}"
                    >
                        {{ status.numText }}
                    </div>
                </div>
                <div class="stat-box-data-unit" ref="dataUnitRef">
                    {{ status.unit }}
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import {useMainStore} from "../store/MainStore";

export default defineComponent({
    data() {
        return {
            statuses: [] as {
                title: string,
                numText: string,
                unit: string,
                fontSize?: number
                maxWidth?: number
            }[],
            numMaxFontsize: 18, // px = 1.125rem = text-lg
            isMobile: (document.documentElement.clientWidth < 1024),
            screenWidth: document.documentElement.clientWidth
        }
    },
    setup() {
        const store = useMainStore();
        return {store};
    },
    mounted() {
        window.addEventListener("resize", this.onWindowSizeChanged);
        this.statuses = [
            {
                title: "発展ポイント",
                numText: this.store.status.development_points.toLocaleString(),
                unit: "pts",
            },
            {
                title: "人口",
                numText: this.store.status.population.toLocaleString(),
                unit: "人"
            },
            {
                title: "資金",
                numText: this.store.status.funds.toLocaleString(),
                unit: "億円"
            },
            {
                title: "食料",
                numText: this.store.status.foods.toLocaleString(),
                unit: "㌧"
            },
            {
                title: "資源",
                numText: this.store.status.resources.toLocaleString(),
                unit: "㌧"
            },
            {
                title: "環境",
                numText: this.store.getEnvironmentString,
                unit: ""
            },
            {
                title: "面積",
                numText: this.store.status.area.toLocaleString(),
                unit: "万坪"
            },
            {
                title: "農業",
                numText: this.store.status.foods_production_capacity.toLocaleString(),
                unit: "人規模"
            },
            {
                title: "工業",
                numText: this.store.status.funds_production_capacity.toLocaleString(),
                unit: "人規模"
            },
            {
                title: "資源生産",
                numText: this.store.status.resources_production_capacity.toLocaleString(),
                unit: "人規模"
            },
        ]
        // フォントサイズのデフォルト値設定
        this.statuses.forEach(status => {
            status.fontSize = 1;
            status.maxWidth = 0;
        });

        this.$nextTick(() => {
            this.updateFontSize();
        });
    },
    unmounted() {
        window.removeEventListener("resize", this.onWindowSizeChanged);
    },
    methods: {
        updateFontSize() {
            this.statuses.forEach((status, index) => {
                const parentWidth = this.$refs.dataNumRef[index].parentNode.clientWidth;
                const unitWidth = this.$refs.dataUnitRef[index].offsetWidth;
                const maxWidth = Math.floor(this.isMobile ? parentWidth : parentWidth - unitWidth);
                status.maxWidth = maxWidth;
                status.fontSize = this.calcFontSize(status.numText, maxWidth);
            })
        },
        calcFontSize(text: string, maxWidth: number) {
            const span = document.createElement("span");
            span.style.width = "0px";
            span.style.maxWidth = maxWidth + "px";
            span.style.fontSize = "10px";
            span.textContent = text;
            document.body.appendChild(span);

            let size = 1;
            span.style.fontSize = size + "px";

            while (span.offsetWidth <= maxWidth && size < maxWidth && size <= this.numMaxFontsize) {
                size++;
                span.style.fontSize = size + "px";
            }
            size--;

            if (size < 1) size = 1;
            span.parentNode.removeChild(span);
            return size;
        },
        onWindowSizeChanged() {
            const newScreenWidth = document.documentElement.clientWidth;
            if (this.screenWidth != newScreenWidth) {
                this.isMobile = (document.documentElement.clientWidth < 1024);
                this.updateFontSize();
            }
        },
    }
});
</script>

<style lang="scss" scoped>
.stats {
    @apply container px-2 py-1 md:mx-auto mb-3 md:mb-6 max-w-full grid grid-cols-3 md:grid-cols-5 gap-2;

    .stats-box {
        @apply bg-surface-variant rounded-xl p-2 drop-shadow-md;

        .stats-box-title {
            @apply font-bold text-left text-on-surface-variant text-xs lg:text-sm;
        }

        .stats-box-data {
            @apply flex items-end flex-wrap h-8;

            .stats-box-num-wrapper {
                @apply grow text-right;
            }

            .stats-box-data-num {
                @apply font-bold w-full inline ;
            }

            .stat-box-data-unit {
                @apply max-lg:w-full max-lg:-mt-1.5 text-[0.6rem] lg:text-sm text-right lg:pl-2;
            }
        }
    }
}
</style>
