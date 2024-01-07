<template>
    <div id="footer">
        <div class="footer-contents">
            <div class="footer-main">
                <div class="footer-head">
                    <img class="brand-logo" :src="'/favicon.ico'" />
                    <span class="brand-title">やまにてぃ（仮）</span>
                </div>
                <div class="footer-grid">
                    <div v-for="linkGroup of linkGroups" class="footer-links">
                        <div class="links-title">
                            {{linkGroup.title}}
                        </div>
                        <a v-for="link of linkGroup.links" class="link" :href="link.href">
                            {{link.name}}
                        </a>
                    </div>
                </div>
            </div>
            <div class="footer-icons">
                <a class="icon-link">
                    <FontAwesomeIcon class="icon" icon="fa-brands fa-twitter" size="xl" />
                </a>
                <a class="icon-link" href="https://github.com/mjtakenon/hakoniwa">
                    <FontAwesomeIcon class="icon" icon="fa-brands fa-github" size="xl" />
                </a>
                <a class="icon-link">
                    <FontAwesomeIcon class="icon" icon="fa-brands fa-discord" size="xl" />
                </a>
            </div>
            <div class="footer-credits">
                <div class="font-bold text-sm text-on-surface-variant">クレジット</div>
                <div class="px-2">
                    <span class="max-md:block">箱庭諸島スクリプト配布元 :</span>
                    <a class="max-md:block max-md:my-0 mx-[15px]" href="http://www.bekkoame.ne.jp/~tokuoka/hakoniwa.html">http://www.bekkoame.ne.jp/~tokuoka/hakoniwa.html</a>
                </div>
                <div class="px-2">
                    <span class="max-md:block">Hakoniwa R.A.スクリプト配布元 :</span>
                    <a class="max-md:block max-md:my-0 mx-[15px]" href="http://www5b.biglobe.ne.jp/~k-e-i/">http://www5b.biglobe.ne.jp/~k-e-i/</a>
                </div>
                <div class="px-2">
                    <span class="max-md:block">Yahoo Login :</span>
                    <!-- Begin Yahoo! JAPAN Web Services Attribution Snippet -->
                    <span style="margin:15px 15px 15px 15px"><a href="https://developer.yahoo.co.jp/sitemap/">Webサービス by Yahoo! JAPAN</a></span>
                    <!-- End Yahoo! JAPAN Web Services Attribution Snippet -->
                </div>            </div>
        </div>
    </div>
</template>

<script lang="ts">

import {defineComponent} from "vue";
import {library} from "@fortawesome/fontawesome-svg-core";
import {faTwitter} from "@fortawesome/free-brands-svg-icons";
import {faGithub} from "@fortawesome/free-brands-svg-icons";
import {faDiscord} from "@fortawesome/free-brands-svg-icons";

interface LinkGroup {
    title: string,
    links: {
        name: string,
        href: string,
    }[]
}

export default defineComponent({
    data() {
        return {
            linkGroups: [
                {
                    title: "一般",
                    links: [
                        { name: "トップページ", href: "/" },
                        { name: "更新情報", href: "/releases" },
                        { name: "プライバシーポリシー", href: "/privacy" },
                    ]
                },
                {
                    title: "マニュアル",
                    links: [
                        { name: "遊び方", href: "/help" },
                        { name: "コマンド一覧", href: "/help" },
                        { name: "地形一覧", href: "/help" },
                    ]
                },
            ] as LinkGroup[]
        }
    },
    setup() {
        library.add(faTwitter, faGithub, faDiscord);
    }
})

</script>

<style scoped lang="scss">
#footer {
    @apply w-full max-md:px-5 mt-14 py-4 bg-surface-variant text-on-surface;

    .footer-contents {
        @apply mx-auto max-w-[1000px];

        .footer-main {
            @apply w-full flex items-center border-b border-on-surface-variant my-2;

            .footer-head {
                @apply w-1/2 flex pl-10 items-center max-md:hidden;

                .brand-logo {
                    @apply mr-10 w-14 h-14 dark:filter dark:invert;
                }

                .brand-title{
                    @apply font-bold text-xl;
                }
            }

            .footer-grid {
                @apply grid grid-cols-2 pb-6;
                // sp
                @apply w-full;
                // desktop
                @apply md:w-1/2 md:px-10;

                .footer-links {
                    @apply text-sm text-on-surface-variant;

                    .links-title {
                        @apply font-bold mb-2;
                    }

                    .link {
                        @apply block ml-2 mb-1;
                    }
                }
            }
        }

        .footer-icons {
            @apply mt-2 px-2 flex h-12 items-center justify-end;

            .icon-link {
                @apply block text-on-surface-variant mr-5;
            }

            .icon-link:last-of-type {
                @apply mr-1;
            }
        }

        .footer-credits {
            @apply w-full mx-auto my-2 px-4 mt-8;
        }
    }
}
</style>
