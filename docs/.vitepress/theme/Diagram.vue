<script setup lang="ts">
// A diagram from public/diagrams/<name>-light.svg and -dark.svg, the variant matching the theme.
import { withBase } from 'vitepress'

defineProps<{ name: string, alt: string }>()
</script>

<template>
  <figure class="ic-diagram">
    <a :href="withBase(`/diagrams/${name}-light.svg`)" target="_blank" rel="noopener" :aria-label="`${alt} (open full size)`">
      <img class="only-light" :src="withBase(`/diagrams/${name}-light.svg`)" :alt="alt" loading="lazy" decoding="async">
      <img class="only-dark" :src="withBase(`/diagrams/${name}-dark.svg`)" :alt="alt" loading="lazy" decoding="async">
    </a>
    <figcaption class="ic-diagram-caption">Open the diagram full size</figcaption>
  </figure>
</template>
