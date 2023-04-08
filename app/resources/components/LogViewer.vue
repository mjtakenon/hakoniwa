<template>
    <div id="logs">
        <div class="subtitle">
            {{ island.name }}島の近況
        </div>
        <div v-for="log of islandLog" :key="log.id">
<!--            ターン {{ log.turn_id }} :-->
            <span v-for="context of JSON.parse(log.log)" :key="context.text">
                <a v-if="context.hasOwnProperty('link')" :href="context.link" :style="context.style">
                    {{ context.text }}
                </a>
                <span v-else :style="context.style">
                    {{ context.text }}
                </span>
            </span>
        </div>
    </div>
</template>
<script>
export default {
    props: ['island', 'islandLog'],
}
</script>

<style lang="scss" scoped>
@import "bulma/bulma.sass";

#logs {
    text-align: left;
    margin-left: 30px;
    max-width: 800px;
}
</style>
