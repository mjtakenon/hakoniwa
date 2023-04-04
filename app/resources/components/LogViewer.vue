<template>
    <div id="logs">
        {{ island.name }}島のログ
        <div v-for="log of islandLog" key="id">
            ターン {{ log.turn_id }} : {{ log.log }}
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
