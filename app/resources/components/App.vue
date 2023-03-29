<template>
    <div class="wrapper">
        <!-- <v-header></v-header> -->
        <dev class="test">
            hoge
        </dev>
        
        <dev>
            {{ hello }}
        </dev>
    </div>
</template>

<script lang="ts">
import { ref } from "vue";
export default {
    setup() {
        const hello = ref(
            "Hello From TypeScript"
        )

        return {
            hello
        }
    },
};
</script>

<style lang="scss" scoped>
@import "bulma/bulma.sass";
.test {
    color: blue;
}
</style>