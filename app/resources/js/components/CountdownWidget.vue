<template>
    <div id="countdown-widget">
        <div class="countdown-box rounded-br-xl md:rounded-b-xl">
            <span class="countdown-label mr-1">turn:</span>
            <span class="text-sm">{{ store.turn.turn }}</span>
        </div>
        <div
            class="countdown-box rounded-bl-xl md:rounded-b-xl"
            :class="{'countdown-box-updated': !this.isTimeRemaining}"
        >
            <template v-if="isTimeRemaining">
                <span class="countdown-label mr-1">次の更新まで</span>
                <template v-if="remainTimes.hour > 0">
                    <span class="text-sm">{{ remainTimes.hour }}</span>
                    <span class="countdown-unit">時間</span>
                </template>
                <template v-if="remainTimes.hour > 0 || remainTimes.min > 0">
                    <span class="text-sm">{{
                            (remainTimes.min < 10 ? '0' + remainTimes.min : remainTimes.min.toString())
                        }}</span>
                    <span class="countdown-unit">分</span>
                </template>
                <span class="text-sm">{{
                        (remainTimes.sec < 10 ? '0' + remainTimes.sec : remainTimes.sec.toString())
                    }}</span>
                <span class="countdown-unit">秒</span>
            </template>
            <template v-else>
                <span class="text-sm">更新済み</span>
            </template>
        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import {useMainStore} from "../store/MainStore";

export default defineComponent({
    data() {
        return {
            remainTimes: {
                hour: 0,
                min: 0,
                sec: 0
            },
            isTimeRemaining: true,
            secInterval: 0,
        }
    },
    setup() {
        const store = useMainStore();
        return {store};
    },
    mounted() {
        this.secInterval = setInterval(this.countDownTurn, 1000);
    },
    unmounted() {
        clearInterval(this.secInterval);
    },
    methods: {
        countDownTurn() {
            const now = new Date();
            if (now > this.store.turn.next_time) {
                this.isTimeRemaining = false;
                clearInterval(this.secInterval);
            } else {
                const diff = this.store.turn.next_time.getTime() - now.getTime();
                const h = Math.floor(diff / 1000 / 60 / 60);
                const m = Math.floor(diff / 1000 / 60) % 60;
                const s = Math.floor(diff / 1000) % 60;
                this.remainTimes = {
                    hour: h,
                    min: m,
                    sec: s
                };
            }
        }
    }
})
</script>

<style scoped lang="postcss">

#countdown-widget {
    @apply flex w-full justify-between h-6 mb-4 lg:mb-2 drop-shadow;

    .countdown-box {
        @apply inline-flex items-center px-2 bg-gray-300;
    }

    .countdown-box-updated {
        @apply bg-danger-dark text-white;
    }

    .countdown-label {
        @apply text-xs text-gray-600;
    }

    .countdown-unit {
        @apply text-[8px] mt-auto mb-[2px] mr-1 text-gray-600;
    }

    .countdown-unit:last-child {
        @apply mr-0
    }
}
</style>
