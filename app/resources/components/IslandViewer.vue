<template>
    <div id="island-viewer" class="wrapper">
        <div>
            {{ hello }}
        </div>
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
    mounted() {
        console.log(this.island);
        console.log(this.islandStatus);
        console.log(this.islandTerrain);
        console.log(this.islandLog);
    },
    props: ['island', 'islandStatus', 'islandTerrain', 'islandLog']
};
</script>

<style lang="scss" scoped>
@import "bulma/bulma.sass";
.test {
    color: blue;
}
</style>
