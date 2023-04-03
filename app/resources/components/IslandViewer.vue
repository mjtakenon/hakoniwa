<template>
    <div id="island-viewer" class="wrapper">
        <div id=""><a href="/">TOPへ戻る</a></div>
        <div id="">{{ island.name }}島へようこそ！</div>
        <div id="status">人口: {{ islandStatus.population }}</div>
        <div class="is-flex">
            <div v-for="y of 15" key="y">
                <div v-for="x of 15" key="x" class="row">
                    <img :src="getIslandTerrain(x-1,y-1).data.image_path" class="cell">
                </div>
            </div>
<!--            <div v-for="row in islandTerrain" class="">-->
<!--                <img :src="row.data.image_path">-->
<!--                <br v-if="row.data.point.y === 14"/>-->
<!--            </div>-->
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
    methods: {
        getIslandTerrain(x, y) {
            return this.islandTerrain.filter(function(item, idx){
                if (item.data.point.x === x && item.data.point.y === y) return true;
            })[0];
        }
    },
    mounted() {
        // console.log(this.island);
        // console.log(this.islandStatus);
        // console.log(this.islandTerrain);
        // console.log(this.islandLog);
    },
    // methods() {
    // },
    props: ['island', 'islandStatus', 'islandTerrain', 'islandLog'],
};
</script>

<style lang="scss" scoped>
@import "bulma/bulma.sass";

#island-viewer {
    text-align: center;
    margin: 0 auto;
    min-width: 600px;
}

.row {
    max-height: 32px;
}

.cell {
    max-height: 32px;
    min-height: 32px;
    max-width: 32px;
    min-width: 32px;
}
</style>
