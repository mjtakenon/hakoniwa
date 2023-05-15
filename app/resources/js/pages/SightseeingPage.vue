<template>
    <div id="sightseeing-page" class="wrapper">
        <div class="title mt-2">{{ store.island.name }}島へようこそ！</div>
        <div class="link-text mb-5"><a href="/">トップへ戻る</a></div>
        <status-table></status-table>
        <hr/>
        <island-viewer></island-viewer>
        <hr/>
        <log-viewer></log-viewer>
    </div>
</template>

<script lang="ts">
import StatusTable from "../components/StatusTable.vue";
import LogViewer from "../components/LogViewer.vue";
import IslandViewer from "../components/IslandViewer.vue";
import {defineComponent, PropType} from "vue";
import {useMainStore} from "../store/MainStore";
import {Hakoniwa} from "../store/Entity/Hakoniwa";
import {Status} from "../store/Entity/Status";
import {Terrain} from "../store/Entity/Terrain";
import {Plan} from "../store/Entity/Plan";
import {LogParser, LogProps, SummaryProps} from "../store/Entity/Log";

export default defineComponent({
    components: {
        IslandViewer,
        StatusTable,
        LogViewer,
    },
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
    setup(props) {
        const store = useMainStore();
        const parser = new LogParser();
        const logs = parser.parse(props.island.logs, props.island.summary);

        store.$patch({
            hakoniwa: props.hakoniwa,
            island: props.island,
            status: props.island.status,
            terrains: props.island.terrains,
            logs: logs
        })
        return { store }
    },
    methods: {},
    computed: {
        // showHoverWindow() { return true; }
    },
    props: {
        hakoniwa: {
            required: true,
            type: Object as PropType<Hakoniwa>
        },
        // TODO: ここで飛んでくるislandはPlansController.phpで定義されており、js/store/Entity/Islandの中身と異なっている　共通化できないか？
        island: {
            required: true,
            type: Object as PropType<{
                id: number,
                name: string,
                owner_name: string,
                status: Status,
                terrains: Array<Terrain>,
                plans: Array<Plan>,
                logs: LogProps[]
            }>
        },
    },
});
</script>

<style lang="scss" scoped>

#sightseeing-page {
    @apply text-center mx-auto max-w-[1000px] max-md:overflow-x-scroll min-h-[1200px]
}

</style>
