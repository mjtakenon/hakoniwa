<template>
    <div id="island">
        <div class="row m-0 p-0" v-for="y of hakoniwa.height" key="y">
            <div class="right-padding" v-if="y%2 === 1">
                {{ y }}
            </div>
            <div class="cell" v-for="x of hakoniwa.width" key="x">
                <img
                    @mouseover="onMouseOverCell(x-1, y-1)"
                    @mouseleave="onMouseLeaveCell(x-1, y-1)"
                    :src="getIslandTerrain(x-1,y-1).data.image_path"
                    :alt="getIslandTerrain(x-1,y-1).data.type"
                    class="cell"
                >
            </div>
            <div class="left-padding" v-if="y%2 === 0"></div>
        </div>
        <div v-show="showHoverWindow" class="hover-window" :style="{ top: hoverWindowTop+'px', left: hoverWindowLeft+'px' }">
            <div>
                <img
                    class="hover-window-img"
                    :src="getIslandTerrain(hoverCell.x, hoverCell.y).data.image_path"
                >
                {{ getIslandTerrain(hoverCell.x, hoverCell.y).data.info }}
            </div>
        </div>
    </div>
</template>

<script lang="ts">
export default {
    components: {},
    data() {
        return {
            showHoverWindow: false,
            hoverCell: {
                "x": 0,
                "y": 0,
            },
            hoverWindowTop: 170,
            hoverWindowLeft: 0,
        }
    },
    setup() {
    },
    methods: {
        getIslandTerrain(x, y) {
            return this.islandTerrain.filter(function(item, idx){
                if (item.data.point.x === x && item.data.point.y === y) return true;
            }).pop();
        },
        onMouseOverCell(x, y) {
            this.showHoverWindow = true;
            this.hoverCell.x = x;
            this.hoverCell.y = y;

            // 左半分
            if (this.hoverCell.x < this.hakoniwa.width / 2) {
                this.hoverWindowLeft = 250;
            } else {
                this.hoverWindowLeft = 0;
            }
        },
        onMouseLeaveCell(x, y) {
            this.showHoverWindow = false;
        }
    },
    mounted() {
        // console.log(this.$props)
    },
    computed: {},
    props: ['hakoniwa', 'island', 'islandStatus', 'islandTerrain', 'islandLog'],
};
</script>

<style lang="scss" scoped>
@import "bulma/bulma.sass";

#island {
    position: relative;
    margin: 0 auto;
    max-width: 480px;
    min-width: 496px;
    min-height: 496px;
}

.row {
    display: grid;
    grid-template-columns: repeat(16, 1fr);
}

.cell {
    width: 32px;
    height: 32px;
}

.left-padding {
    width: 16px;
    height: 32px;
    background-image: url("/img/hakoniwa/hakogif/land0.gif");
    background-position: left;
}

.right-padding {
    width: 16px;
    height: 32px;
    background-image: url("/img/hakoniwa/hakogif/land0.gif");
    background-position: right;

    color: white;
    font-size: 10px;
    padding-top: 8px;
}

.hover-window {
    text-align: left;
    padding: 10px;
    margin: 10px;
    position: absolute;
    border: 1px solid;
    background-color: lightyellow;
    min-width: 200px;
    min-height: 50px;
}

.hover-window-img {
    vertical-align: top;
}

</style>
