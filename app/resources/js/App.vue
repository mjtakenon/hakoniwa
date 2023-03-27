<template>
    <div>{{ hello }}</div>
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
</style>