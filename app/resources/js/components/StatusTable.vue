<template>
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
</template>

<script>
export default {
    props: ['islandStatus', ],
}
</script>

<style lang="scss" scoped>

#status {
    margin: 0 auto;
    min-width: 480px;
}

</style>
