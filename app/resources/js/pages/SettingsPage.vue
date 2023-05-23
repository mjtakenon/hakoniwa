<template>
    <div id="settings">
        <div class="settings-contents">
            <h1 class="title">ユーザ設定</h1>
            <island-name-settings
                :change_island_name_price="change_island_name_price"
            >
            </island-name-settings>
        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent, PropType} from 'vue'
import {Status} from "../store/Entity/Status";
import {useMainStore} from "../store/MainStore";
import IslandNameSettings from "../components/IslandNameSettings.vue";

export default defineComponent({
    components: {IslandNameSettings},
    setup(props) {
        const store = useMainStore();
        store.$patch({
            island: {
                id: props.island.id,
                name: props.island.name,
                owner_name: props.island.owner_name
            },
            status: props.island.status,
        });
        return {store};
    },
    props: {
        island: {
            required: true,
            type: Object as PropType<{
                id: number,
                name: string,
                owner_name: string,
                status: Status,
            }>
        },
        change_island_name_price: {
            required: true,
            type: Number
        }
    },
})
</script>

<style scoped lang="scss">
#settings {
    @apply w-full min-h-[100vh];

    .settings-contents {
        @apply mx-auto max-w-3xl;

        .title {
            @apply border-b p-2 pb-4 mb-10;
        }
    }
}
</style>
