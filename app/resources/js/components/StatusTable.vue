<template>
    <div class="stats">
        <div
            v-for="(status, index) in statuses"
            class="stats-box"
            :class="{'max-md:col-span-3': (index === 0)}"
        >
            <div class="stats-box-title">
                {{ status.title }}
            </div>
            <div class="stats-box-data">
                <div class="stats-box-data-num">
                    {{ status.num }}
                </div>
                <div class="stat-box-data-unit">
                    {{ status.unit }}
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";

const ISLAND_ENVIRONMENT = {
    'best': '最高',
    'good': '良好',
    'normal': '通常',
}

export default defineComponent({
    data() {
        return {
            statuses: [
                {
                    title: "発展ポイント",
                    num: this.$store.state.status.development_points,
                    unit: "pts"
                },
                {
                    title: "人口",
                    num: this.$store.state.status.population,
                    unit: "人"
                },
                {
                    title: "資金",
                    num: this.$store.state.status.funds,
                    unit: "億円"
                },
                {
                    title: "食料",
                    num: this.$store.state.status.foods,
                    unit: "㌧"
                },
                {
                    title: "資源",
                    num: this.$store.state.status.resources,
                    unit: "㌧"
                },
                {
                    title: "環境",
                    num: ISLAND_ENVIRONMENT[this.$store.state.status.environment],
                    unit: ""
                },
                {
                    title: "面積",
                    num: this.$store.state.status.area,
                    unit: "万坪"
                },
                {
                    title: "農業",
                    num: this.$store.state.status.foods_production_number_of_people,
                    unit: "人規模"
                },
                {
                    title: "工業",
                    num: this.$store.state.status.funds_production_number_of_people,
                    unit: "人規模"
                },
                {
                    title: "資源生産",
                    num: this.$store.state.status.resources_production_number_of_people,
                    unit: "人規模"
                },
            ],
        }
    },
});
</script>

<style lang="postcss" scoped>
.stats {
    @apply container px-2 py-1 md:mx-auto mb-3 md:mb-6 max-w-full grid grid-cols-3 md:grid-cols-5 gap-2;

    .stats-box {
        @apply bg-gray-200 rounded-xl p-2 drop-shadow-md;

        .stats-box-title {
            @apply font-bold text-left text-gray-600 text-xs md:text-sm;
        }

        .stats-box-data {
            @apply flex items-end;

            .stats-box-data-num {
                @apply grow text-lg text-right font-bold mr-2;
            }

            .stat-box-data-unit {
                @apply text-xs md:text-sm text-right;
            }
        }
    }
}
</style>
