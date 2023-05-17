<template>
    <div class="grid grid-cols-2 gap-4">
        <div class="turn-box">
            <h2 class="turn-box-title">ターン</h2>
            <p class="turn-box-text-big h-2/3 m-0"> {{ $props.turn }}  </p>
        </div>
        <div class="turn-box">
            <h2 class="turn-box-title">次回更新予定</h2>
            <p class="turn-box-text-big max-md:hidden"> {{ $props.day + " " + $props.time }} </p>
            <p class="turn-box-text-medium md:hidden mb-0"> {{ $props.day }} </p>
            <p class="turn-box-text-medium md:hidden -mt-1"> {{ $props.time }} </p>
        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";

export default defineComponent({
    props: ['turn', 'day', 'time']
});
</script>

<style lang="scss" scoped>
.turn-box {
    @apply bg-primary text-on-primary p-1 rounded drop-shadow-md;

    .turn-box-title {
        @apply font-bold;
    }

    .turn-box-text-big, .turn-box-text-medium {
        @apply text-center font-black;
    }

    .turn-box-text-big {
        @apply text-3xl
    }

    .turn-box-text-medium {
        @apply text-lg;
    }
}


</style>
