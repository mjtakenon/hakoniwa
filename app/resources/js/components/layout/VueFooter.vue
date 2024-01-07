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
              {{ linkGroup.title }}
            </div>
            <a v-for="link of linkGroup.links" class="link" :href="link.href">
              {{ link.name }}
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
        <div class="text-sm font-bold text-on-surface-variant">クレジット</div>
        <div class="px-2">
          <span class="max-md:block">箱庭諸島スクリプト配布元 :</span>
          <a class="mx-[15px] max-md:my-0 max-md:block" href="http://www.bekkoame.ne.jp/~tokuoka/hakoniwa.html"
            >http://www.bekkoame.ne.jp/~tokuoka/hakoniwa.html</a
          >
        </div>
        <div class="px-2">
          <span class="max-md:block">Hakoniwa R.A.スクリプト配布元 :</span>
          <a class="mx-[15px] max-md:my-0 max-md:block" href="http://www5b.biglobe.ne.jp/~k-e-i/"
            >http://www5b.biglobe.ne.jp/~k-e-i/</a
          >
        </div>
        <div class="px-2">
          <span class="max-md:block">Yahoo Login :</span>
          <!-- Begin Yahoo! JAPAN Web Services Attribution Snippet -->
          <span style="margin: 15px 15px 15px 15px"
            ><a href="https://developer.yahoo.co.jp/sitemap/">Webサービス by Yahoo! JAPAN</a></span
          >
          <!-- End Yahoo! JAPAN Web Services Attribution Snippet -->
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faTwitter } from '@fortawesome/free-brands-svg-icons'
import { faGithub } from '@fortawesome/free-brands-svg-icons'
import { faDiscord } from '@fortawesome/free-brands-svg-icons'

interface LinkGroup {
  title: string
  links: {
    name: string
    href: string
  }[]
}

export default defineComponent({
  data() {
    return {
      linkGroups: [
        {
          title: '一般',
          links: [
            { name: 'トップページ', href: '/' },
            { name: '更新情報', href: '/releases' },
            { name: 'プライバシーポリシー', href: '/privacy' }
          ]
        },
        {
          title: 'マニュアル',
          links: [
            { name: '遊び方', href: '/help' },
            { name: 'コマンド一覧', href: '/help' },
            { name: '地形一覧', href: '/help' }
          ]
        }
      ] as LinkGroup[]
    }
  },
  setup() {
    library.add(faTwitter, faGithub, faDiscord)
  }
})
</script>

<style scoped lang="scss">
#footer {
  @apply mt-14 w-full bg-surface-variant py-4 text-on-surface max-md:px-5;

  .footer-contents {
    @apply mx-auto max-w-[1000px];

    .footer-main {
      @apply my-2 flex w-full items-center border-b border-on-surface-variant;

      .footer-head {
        @apply flex w-1/2 items-center pl-10 max-md:hidden;

        .brand-logo {
          @apply mr-10 h-14 w-14 dark:invert dark:filter;
        }

        .brand-title {
          @apply text-xl font-bold;
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
            @apply mb-2 font-bold;
          }

          .link {
            @apply mb-1 ml-2 block;
          }
        }
      }
    }

    .footer-icons {
      @apply mt-2 flex h-12 items-center justify-end px-2;

      .icon-link {
        @apply mr-5 block text-on-surface-variant;
      }

      .icon-link:last-of-type {
        @apply mr-1;
      }
    }

    .footer-credits {
      @apply mx-auto my-2 mt-8 w-full px-4;
    }
  }
}
</style>
