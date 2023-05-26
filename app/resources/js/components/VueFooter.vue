<template>
    <div id="footer">
        <div class="footer-contents">
            <div class="footer-main">
                <div class="footer-head">
                    <img class="brand-logo" src="/favicon.ico" />
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
                    <font-awesome-icon class="icon" icon="fa-brands fa-twitter" size="xl" />
                </a>
                <a class="icon-link" href="https://github.com/mjtakenon/hakoniwa">
                    <font-awesome-icon class="icon" icon="fa-brands fa-github" size="xl" />
                </a>
                <a class="icon-link">
                    <font-awesome-icon class="icon" icon="fa-brands fa-discord" size="xl" />
                </a>
            </div>
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
                        { name: "ヘルプ", href: "/help" },
                    ]
                },
                {
                    title: "配布元",
                    links: [
                        { name: "箱庭諸島", href: "http://www.bekkoame.ne.jp/~tokuoka/hakoniwa.html" },
                        { name: "Hakoniwa R.A.", href: "http://www5b.biglobe.ne.jp/~k-e-i/" },
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
            @apply w-full flex items-center border-b my-2;

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
    }
}
</style>
