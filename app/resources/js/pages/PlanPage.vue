<template>
    <div id="plan-page">
        <div class="title mt-2">{{ store.island.name }}島開発計画</div>
        <div class="link-text mb-5"><a href="/">トップへ戻る</a></div>
        <status-table></status-table>
        <comment-form></comment-form>
        <div class="flex flex-wrap items-stretch mx-auto justify-center mt-10">
            <plan-controller
                class="grow"
                :class="{'order-2' : !canSideBySide}"
            ></plan-controller>
            <div
                class="z-30"
                :class="{'w-full order-1': !canSideBySide}"
            >
                <island-editor></island-editor>
            </div>
            <plan-list
                class="grow"
                :class="{'order-2' : !canSideBySide}"
            ></plan-list>
        </div>
        <div class="md:max-lg:px-3">
            <island-bbs></island-bbs>
            <log-viewer
                :title="store.island.name + '島の近況'"
                :parsed-logs="store.logs"
            ></log-viewer>
        </div>
        <island-popup></island-popup>
    </div>
</template>

<script lang="ts">
import StatusTable from "../components/StatusTable.vue";
import LogViewer from "../components/LogViewer.vue";
import IslandEditor from "../components/IslandEditor.vue";
import PlanController from "../components/PlanController.vue";
import PlanList from "../components/PlanList.vue";
import lodash from 'lodash';
import {defineComponent, PropType} from "vue";
import {useMainStore} from "../store/MainStore";

import {Hakoniwa} from "../store/Entity/Hakoniwa";
import {Island} from "../store/Entity/Island";
import {Status} from "../store/Entity/Status";
import {Terrain} from "../store/Entity/Terrain";
import {Plan} from "../store/Entity/Plan";
import {Turn} from "../store/Entity/Turn";
import {LogParser, LogProps, SummaryProps} from "../store/Entity/Log";
import IslandPopup from "../components/IslandPopup.vue";
import CommentForm from "../components/CommentForm.vue";
import {AchievementProp, getAchievementsList} from "../store/Entity/Achievement";
import IslandBbs from "../components/IslandBbs.vue";
import {BbsMessage} from "../store/Entity/Bbs";

export default defineComponent({
    components: {
        IslandBbs,
        CommentForm,
        IslandEditor,
        IslandPopup,
        PlanController,
        StatusTable,
        LogViewer,
        PlanList,
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
            screenWidth: document.documentElement.clientWidth,
        }
    },
    setup(props) {
        // props.planCandidateの分解・Plan[]に再構築
        const candidates: Plan[] = []
        for (const [key, value] of Object.entries(props.planCandidate)) {
            candidates.push({
                key: key,
                data: value
            })
        }

        // turn.next_turnのDateオブジェクト変換
        const turn: Turn = {
            turn: props.turn.turn,
            next_time: new Date(props.turn.next_time)
        }

        // Logsのparse
        const parser = new LogParser();
        const logs = parser.parse(props.island.logs, props.island.summary);

        // Achievementの変換
        const achievements = getAchievementsList(props.island.achievements);

        // Pinia
        const store = useMainStore();
        store.$patch({
            hakoniwa: props.hakoniwa,
            island: props.island,
            status: props.island.status,
            terrains: props.island.terrains,
            logs: logs,
            plans: lodash.cloneDeep(props.island.plans),
            sentPlans: lodash.cloneDeep(props.island.plans),
            planCandidate: candidates,
            targetIslands: props.targetIslands,
            selectedTargetIsland: props.island.id,
            turn: turn,
            achievements: achievements,
            bbs: props.island.bbs,
        });
        return {store}
    },
    computed: {
        canSideBySide() {
            return this.screenWidth > 912;
        }
    },
    mounted() {
        window.addEventListener("resize", this.onWindowSizeChanged);
    },
    unmounted() {
        window.removeEventListener("resize", this.onWindowSizeChanged);
    },
    methods: {
        onWindowSizeChanged() {
            const newScreenWidth = document.documentElement.clientWidth;
            if (this.screenWidth !== newScreenWidth) {
                this.screenWidth = newScreenWidth;
            }
        },
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
                logs: LogProps[],
                summary: SummaryProps[]
                comment?: string,
                achievements: AchievementProp[],
                bbs: BbsMessage[],
            }>
        },
        planCandidate: {
            required: true,
            type: Object as PropType<{ [K in string]: Plan["data"] }>
        },
        targetIslands: {
            required: true,
            type: Array as PropType<Island[]>
        },
        turn: {
            required: true,
            type: Object as PropType<{
                turn: number,
                next_time: string
            }>
        },
    },
});
</script>

<style lang="scss" scoped>

#plan-page {
    @apply text-center mx-auto max-w-[1000px] min-h-[1200px];
}

</style>
