<template>
    <div
        id="plan-list"
        :class="[isPlanSent ? 'bg-gray-200' : 'bg-danger-light']"
    >
        <div class="send-status">
            <div v-if="isPlanSent" class="send-status-text bg-success"> -- 計画送信済み --</div>
            <div v-else class="send-status-text bg-danger-dark"> -- 計画未送信 --</div>
        </div>
        <div class="plans">
            <div
                v-for="[index, plan] of Object.entries($store.state.plans)"
                :key="plan"
                class="plan"
                @click="onClickPlan(index)"
                :class="{'bg-white/30': parseInt(index)+1 === $store.state.selectedPlanNumber}"
            >
                <!--                :style="[ parseInt(index)+1 === $store.state.selectedPlanNumber ? { textDecoration: 'underline' } : '']"-->
                <span class="plan-index">
                    {{ parseInt(index) + 1 }}
                </span>
                <span class="plan-separator">:</span>
                <div class="plan-desc">
                    <span v-if="plan.data.usePoint">地点 ({{ plan.data.point.x }},{{ plan.data.point.y }}) に</span>
                    <span class="font-bold">{{ plan.data.name }}</span>
                    <span v-if="plan.data.isFiring">
                        <span v-if="plan.data.amount >= 2 && plan.data.useAmount"> ({{ plan.data.amount }}発発射)</span>
                        <span v-else-if="plan.data.amount === 0 && plan.data.useAmount"> (無制限)</span>
                    </span>
                    <span v-else>
                            <span v-if="plan.data.amount >= 2 && plan.data.useAmount"> ({{ plan.data.amount }}回実施)</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">

export default {
    components: {  },
    data() {
        return {
        }
    },
    setup() {
    },
    methods: {
        onClickPlan(index) {
            this.$store.state.selectedPlanNumber = parseInt(index)+1
        }
    },
    mounted() {},
    computed: {
        isPlanSent: function() {
            return JSON.stringify(this.$store.state.plans) === JSON.stringify(this.$store.state.sentPlans)
        }
    },
    props: ['hakoniwa', 'island'],
};
</script>

<style lang="postcss" scoped>
#plan-list {
    @apply rounded-xl mx-1 lg:ml-3 mb-3 p-2 w-[45%] max-w-[230px] lg:max-h-[496px] text-left overflow-x-visible overflow-y-scroll drop-shadow-md;

    .send-status {
        @apply w-full text-center text-white mb-2;

        .send-status-text {
            @apply w-full py-1 rounded-2xl;
        }
    }

    .plans {
        @apply text-sm w-full;

        .plan {
            @apply w-full flex;

            .plan-index {
                @apply min-w-[1rem] mr-0.5;
            }

            .plan-separator {
                @apply min-w-[3%] mr-0.5;
            }

            .plan-desc {
                @apply inline-block grow;
            }
        }
    }
}
</style>
