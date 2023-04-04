<template>
    <div id="island-viewer" class="wrapper">
        <div class="title">{{ island.name }}島へようこそ！</div>
        <div class="table-container">
            <table id="status" class="table is-narrow is-striped">
                <thead>
                    <tr>
                        <th> 発展ポイント </th>
                        <th> 人口 </th>
                        <th> 資金 </th>
                        <th> 食料 </th>
                        <th> 資源 </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td> {{ islandStatus.development_points }} pts</td>
                        <td> {{ islandStatus.population }} 人</td>
                        <td> {{ islandStatus.funds }} 億円</td>
                        <td> {{ islandStatus.foods }} ㌧</td>
                        <td> {{ islandStatus.resources }} ㌧</td>
                    </tr>
                    <tr>
                        <th> 環境 </th>
                        <th> 面積 </th>
                        <th> 農業 </th>
                        <th> 工業 </th>
                        <th> 資源生産 </th>
                    </tr>
                    <tr>
                        <td> {{ islandStatus.environment }}</td>
                        <td> {{ islandStatus.area }} 万坪</td>
                        <td> {{ islandStatus.foods_production_number_of_people }} 人規模</td>
                        <td> {{ islandStatus.funds_production_number_of_people }} 人規模</td>
                        <td> {{ islandStatus.resources_production_number_of_people }} 人規模</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <hr/>
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
                        :src="getIslandTerrain(hoverCell.x, hoverCell.y).data.image_path"
                    >
                    ({{ hoverCell.x }}, {{ hoverCell.y }}) {{ getIslandTerrain(hoverCell.x, hoverCell.y).data.name }}
                </div>
            </div>
        </div>
        <hr/>
        <div id="logs">
            {{ island.name }}島のログ
            <div v-for="log of islandLog" key="id">
                ターン {{ log.turn_id }} : {{ log.log }}
            </div>
        </div>
    </div>
</template>

<script lang="ts">
export default {
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
    computed: {
        // showHoverWindow() { return true; }
    },
    props: ['hakoniwa', 'island', 'islandStatus', 'islandTerrain', 'islandLog'],
};
</script>

<style lang="scss" scoped>
@import "bulma/bulma.sass";

#island-viewer {
    text-align: center;
    margin: 0 auto;
    max-width: 800px;
}

#island {
    position: relative;
    margin: 0 auto;
    max-width: 480px;
    min-width: 496px;
    min-height: 496px;
}

#status {
    margin: 0 auto;
    min-width: 480px;
}

#logs {
    text-align: left;
    margin-left: 30px;
    max-width: 800px;
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

</style>
