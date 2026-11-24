// Strings that the custom theme components render. Pages carry their own text;
// these are the labels inside components shared by the English and Slovak trees.
import { computed } from 'vue'
import { useData } from 'vitepress'

const en = {
  // Suffix of the Slovak SVG files: diagrams/<name><suffix>-light.svg
  svgSuffix: '',
  fullSize: '(open full size)',
  openDiagram: 'Open the diagram full size',
  runnableExample: 'Runnable example',
  runnableAria: (name: string) => `Runnable example examples/${name}`,
  runnableText: (name: string) => `<code>examples/${name}</code> runs against a fake API on every push (the <code>Examples</code> workflow). Run it yourself with <code>examples/ci/recipe.sh ${name}</code>.`,
  viewOnGitHub: 'View on GitHub',
}

export type Strings = typeof en

const sk: Strings = {
  svgSuffix: '-sk',
  fullSize: '(otvoriť v plnej veľkosti)',
  openDiagram: 'Otvoriť diagram v plnej veľkosti',
  runnableExample: 'Spustiteľný príklad',
  runnableAria: (name) => `Spustiteľný príklad examples/${name}`,
  runnableText: (name) => `<code>examples/${name}</code> beží proti simulovanému API pri každom pushi (workflow <code>Examples</code>). Sami ho spustíte príkazom <code>examples/ci/recipe.sh ${name}</code>.`,
  viewOnGitHub: 'Zobraziť na GitHube',
}

export function stringsFor(lang: string): Strings {
  return lang.startsWith('sk') ? sk : en
}

export function useStrings() {
  const { lang } = useData()
  return computed(() => stringsFor(lang.value))
}
