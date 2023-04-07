<template>
    <div id="plan-controller" class="box">

        <div> 動作 </div>
        <div class="is-flex">
            <div class="is-flex-direction-row is-flex-grow-1"><button class="button is-small">挿入</button></div>
            <div class="is-flex-direction-row is-flex-grow-1"><button class="button is-small">上書き</button></div>
            <div class="is-flex-direction-row is-flex-grow-1"><button class="button is-small">削除</button></div>
        </div>

        <hr />

        <div>
            <span style="vertical-align: middle">
                計画番号：
            </span>
            <div class="select is-small">
                <select>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>
            </div>
        </div>

        <hr/>

        <div>
            開発計画 <br/>
            <div class="select is-small">
                <select>
                    <option v-for="plan of planList" key="plan.key"> {{ plan.name }} {{ plan.price }} </option>
                </select>
            </div>
        </div>

        <hr/>

        <div>
            <span style="vertical-align: middle">
                座標：（
            </span>
            <span class="select is-small">
                <select>
                    <option v-for="x of hakoniwa.height" key="x" value="x">
                        {{ x }}
                    </option>
                </select>
            </span>
            <span style="vertical-align: middle">
                ,
            </span>
            <span class="select is-small">
                <select>
                    <option v-for="y of hakoniwa.height" key="y" value="y">
                        {{ y }}
                    </option>
                </select>
            </span>
            <span style="vertical-align: middle">
                ）
            </span>
        </div>

        <hr/>

        <div>
            <span style="vertical-align: middle">
                数量：
            </span>
            <div class="select is-small">
                <select>
                    <option v-for="n of 99" key="n" value="n">
                        {{ n }}
                    </option>
                </select>
            </div>
        </div>

        <hr/>

        <div>
            目標の島 <br/>
            <div class="select is-small">
                <select>
                    <option :value="island.name"> {{ island.name }} 島 </option>
                </select>
            </div>
        </div>

        <hr/>

        <div>
            <span class="is-small" style="vertical-align: middle">
                コマンド移動：
            </span>
            <button class="button is-small"> ▲ </button> - <button class="button is-small"> ▼ </button>
        </div>

        <hr />

        <div>
            <button class="button is-small is-primary">計画送信</button>
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
    },
    mounted() {
        console.log(this.planList);
    },
    computed: {},
    props: ['hakoniwa', 'island', 'islandStatus', 'planList'],
};
</script>

<style lang="scss" scoped>
@import "bulma/bulma.sass";
#plan-controller {
    background-color: #e2e8f0;
    margin: 0 10px 16px 10px;
    padding: 10px;
    min-width: 230px;
    max-height: 496px;
    //font-size: 14px;
}

hr {
    margin: 8px 0;
}

</style>
