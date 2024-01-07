<template>
    <div>
        <div v-show="store.showNotification" :class="notificationClass">
<!--            <button class="delete" @click="onClickNotificationClose()"></button>-->
            {{ notificationMessage }}
        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import {useMainStore} from "../../../../store/MainStore";

export default defineComponent({
    components: {},
    data() {
        return {
            STATUS_PAGE_EXPIRED: 419
        }
    },
    setup() {
        const store = useMainStore();
        return { store };
    },
    methods: {
        onClickNotificationClose() {
            this.store.showNotification = false;
        },
    },
    mounted() {
        // console.log(this.$props)
    },
    computed: {
        notificationMessage() {
            const status = this.store.planSendingResult
            if (status === this.STATUS_PAGE_EXPIRED) return 'セッションの有効期限が切れているため、ページを再読み込みしてください。解決しない場合は、管理者にご連絡ください。(status: ' + status + ')';

            if (status >= 200 && status < 300) {
                return ''
            }
            if (status >= 300 && status < 500) {
                return '不明なエラーが発生しました。時間をおいて再度お試しください。解決しない場合は、管理者にご連絡ください。(status: ' + status + ')';
            }
            return 'サーバーエラーが発生しました。時間をおいて再度お試しください。解決しない場合は、管理者にご連絡ください。(status: ' + status + ')';
        },
        notificationClass() {
            return [
                // 'notification',
                this.store.planSendingResult >= 200 && this.store.planSendingResult < 300 ?
                'has-text-success' : 'has-text-danger'
            ];
        }
    },
    props: [],
});
</script>

<style lang="scss" scoped>

</style>
