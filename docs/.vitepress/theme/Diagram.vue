<script setup lang="ts">
// A diagram from public/diagrams/<name>-light.svg and -dark.svg, the variant matching the theme.
// Slovak pages use the <name>-sk-*.svg variants with Slovak labels.
import { computed } from 'vue'
import { withBase } from 'vitepress'
import { useStrings } from './i18n'

const props = defineProps<{ name: string, alt: string }>()
const t = useStrings()
const file = (variant: string) => withBase(`/diagrams/${props.name}${t.value.svgSuffix}-${variant}.svg`)
const light = computed(() => file('light'))
const dark = computed(() => file('dark'))
</script>

<template>
  <figure class="ic-diagram">
    <a :href="light" target="_blank" rel="noopener" :aria-label="`${alt} ${t.fullSize}`">
      <img class="only-light" :src="light" :alt="alt" loading="lazy" decoding="async">
      <img class="only-dark" :src="dark" :alt="alt" loading="lazy" decoding="async">
    </a>
    <figcaption class="ic-diagram-caption">{{ t.openDiagram }}</figcaption>
  </figure>
</template>
