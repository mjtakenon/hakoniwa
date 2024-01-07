<template>
  <div class="markdown" v-html="html"></div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import MarkdownIt from 'markdown-it'
// Typescript未対応の為
// @ts-ignore
import sanitizer from 'markdown-it-sanitizer'

export default defineComponent({
  setup() {
    const markdown = new MarkdownIt({
      html: true
    }).use(sanitizer)
    return { markdown }
  },
  computed: {
    html() {
      return this.markdown.render(this.sources)
    }
  },
  props: {
    sources: {
      required: true,
      type: String
    }
  }
})
</script>

<style scoped lang="scss"></style>
