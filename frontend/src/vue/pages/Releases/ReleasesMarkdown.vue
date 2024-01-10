<template>
  <div class="markdown" v-html="html"></div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import MarkdownIt from 'markdown-it'
import sanitizer from 'markdown-it-sanitizer'

interface Props {
  sources: string
}
const props = defineProps<Props>()

const markdown = new MarkdownIt({
  html: true
}).use(sanitizer)

const html = computed(() => {
  return markdown.render(props.sources)
})
</script>
