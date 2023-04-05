<template>
    <div id="plan-editor" class="box">
        <div>
            <span style="vertical-align: middle">
                計画番号 :
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
                    <option value="Argentina">Argentina</option>
                    <option value="Bolivia">Bolivia</option>
                    <option value="Brazil">Brazil</option>
                </select>
            </div>
        </div>

        <hr/>

        <div>
            <span style="vertical-align: middle">
                座標 : (
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
                )
            </span>
        </div>

        <hr/>

        <div>
            <span style="vertical-align: middle">
                数量 :
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

        <div> 動作 </div>
        <div class="is-flex">
            <div class="is-flex-direction-row is-flex-grow-1"><button class="button is-small">挿入</button></div>
            <div class="is-flex-direction-row is-flex-grow-1"><button class="button is-small">上書き</button></div>
            <div class="is-flex-direction-row is-flex-grow-1"><button class="button is-small">削除</button></div>
        </div>

        <hr />

        <div>
            <button class="button is-small is-primary">計画送信</button>
        </div>

    </div>
</template>

<script lang="ts">
import IslandViewer from "./IslandViewer.vue";

export default {
    components: { IslandViewer },
    data() {
        return {
        }
    },
    setup() {
    },
    methods: {
    },
    mounted() {
    },
    computed: {},
    props: ['hakoniwa', 'island', 'islandStatus'],
};
</script>

<style lang="scss" scoped>
@import "bulma/bulma.sass";
#plan-editor {
    background-color: #e2e8f0;
    margin: 0 10px 16px 10px;
    padding: 10px;
    min-width: 220px;
    //font-size: 14px;
}

hr {
    margin: 4px 0;
}

</style>
