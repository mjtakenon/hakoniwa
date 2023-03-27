<template>
    <div>
        <dev>
            {{ hello }}
        </dev>
        <dev class="test">
            hoge
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