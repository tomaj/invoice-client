import { defineConfig, type DefaultTheme } from 'vitepress'
import { seoHead, siteUrl } from './seo'

const base = process.env.DOCS_BASE || '/invoice-client/'
const description = 'PHP client for the chcemfakturu.sk invoicing API: build typed invoices, create, fetch and delete them, handle every error as a response, log through PSR-3 and test without the network'
const descriptionSk = 'PHP klient pre fakturačné API chcemfakturu.sk: typované faktúry, ich vytvorenie, načítanie a zmazanie, každá chyba ako odpoveď, logovanie cez PSR-3 a testy bez siete'
const repo = 'https://github.com/tomaj/invoice-client'

const nav: DefaultTheme.NavItem[] = [
  { text: 'Guide', link: '/guide/getting-started', activeMatch: '^/guide/' },
  { text: 'Examples', link: '/examples/', activeMatch: '^/examples/' },
  { text: 'Reference', link: '/api/reference' },
  { text: 'FAQ', link: '/faq' },
  {
    text: '1.x (next)',
    items: [
      { text: 'Changelog', link: '/changelog' },
      { text: 'Upgrade from dev-master', link: '/upgrade' },
      { text: 'Releases', link: `${repo}/releases` },
    ],
  },
]

const navSk: DefaultTheme.NavItem[] = [
  { text: 'Príručka', link: '/sk/guide/getting-started', activeMatch: '^/sk/guide/' },
  { text: 'Príklady', link: '/sk/examples/', activeMatch: '^/sk/examples/' },
  { text: 'Referencia', link: '/sk/api/reference' },
  { text: 'Časté otázky', link: '/sk/faq' },
  {
    text: '1.x (pripravovaná)',
    items: [
      { text: 'Zoznam zmien', link: '/sk/changelog' },
      { text: 'Prechod z dev-master', link: '/sk/upgrade' },
      { text: 'Vydania', link: `${repo}/releases` },
    ],
  },
]

const sidebar: DefaultTheme.SidebarItem[] = [
  {
    text: 'Getting started',
    items: [
      { text: 'Getting started', link: '/guide/getting-started' },
      { text: 'How it works', link: '/guide/how-it-works' },
    ],
  },
  {
    text: 'Guide',
    items: [
      { text: 'Building invoices', link: '/guide/invoices' },
      { text: 'API calls', link: '/guide/api' },
      { text: 'Responses', link: '/guide/responses' },
      { text: 'Error handling', link: '/guide/errors' },
      { text: 'Logging', link: '/guide/logging' },
      { text: 'Transports', link: '/guide/transports' },
      { text: 'Testing your integration', link: '/guide/testing' },
      { text: 'Configuration reference', link: '/guide/configuration' },
      { text: 'Security', link: '/guide/security' },
    ],
  },
  {
    text: 'Examples',
    items: [
      { text: 'Overview', link: '/examples/' },
      { text: 'Create an invoice', link: '/examples/create-invoice' },
      { text: 'Invoice lifecycle', link: '/examples/invoice-lifecycle' },
      { text: 'Error handling', link: '/examples/error-handling' },
      { text: 'Logging', link: '/examples/logging' },
      { text: 'Retries and timeouts', link: '/examples/retries-and-timeouts' },
      { text: 'Testing with PHPUnit', link: '/examples/testing' },
      { text: 'The fake API', link: '/examples/fake-api' },
    ],
  },
  {
    text: 'More',
    items: [
      { text: 'API reference', link: '/api/reference' },
      { text: 'FAQ', link: '/faq' },
      { text: 'Upgrade from dev-master', link: '/upgrade' },
      { text: 'Changelog', link: '/changelog' },
      { text: 'Contributing', link: '/contributing' },
    ],
  },
]

const sidebarSk: DefaultTheme.SidebarItem[] = [
  {
    text: 'Úvod',
    items: [
      { text: 'Začíname', link: '/sk/guide/getting-started' },
      { text: 'Ako to funguje', link: '/sk/guide/how-it-works' },
    ],
  },
  {
    text: 'Príručka',
    items: [
      { text: 'Zostavenie faktúry', link: '/sk/guide/invoices' },
      { text: 'Volania API', link: '/sk/guide/api' },
      { text: 'Odpovede', link: '/sk/guide/responses' },
      { text: 'Spracovanie chýb', link: '/sk/guide/errors' },
      { text: 'Logovanie', link: '/sk/guide/logging' },
      { text: 'Transporty', link: '/sk/guide/transports' },
      { text: 'Testovanie integrácie', link: '/sk/guide/testing' },
      { text: 'Prehľad konfigurácie', link: '/sk/guide/configuration' },
      { text: 'Bezpečnosť', link: '/sk/guide/security' },
    ],
  },
  {
    text: 'Príklady',
    items: [
      { text: 'Prehľad', link: '/sk/examples/' },
      { text: 'Vytvorenie faktúry', link: '/sk/examples/create-invoice' },
      { text: 'Životný cyklus faktúry', link: '/sk/examples/invoice-lifecycle' },
      { text: 'Spracovanie chýb', link: '/sk/examples/error-handling' },
      { text: 'Logovanie', link: '/sk/examples/logging' },
      { text: 'Opakovanie a timeouty', link: '/sk/examples/retries-and-timeouts' },
      { text: 'Testovanie s PHPUnit', link: '/sk/examples/testing' },
      { text: 'Simulované API', link: '/sk/examples/fake-api' },
    ],
  },
  {
    text: 'Ďalšie',
    items: [
      { text: 'Referencia API', link: '/sk/api/reference' },
      { text: 'Časté otázky', link: '/sk/faq' },
      { text: 'Prechod z dev-master', link: '/sk/upgrade' },
      { text: 'Zoznam zmien', link: '/sk/changelog' },
      { text: 'Prispievanie', link: '/sk/contributing' },
    ],
  },
]

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: 'Invoice Client',
  description,
  base,
  srcExclude: ['public/**', 'scripts/**', 'snippets/**', 'node_modules/**'],
  cleanUrls: false,
  lastUpdated: true,

  head: [
    ['link', { rel: 'icon', href: `${base}favicon.ico`, sizes: '48x48' }],
    ['link', { rel: 'icon', type: 'image/svg+xml', href: `${base}favicon.svg` }],
    ['link', { rel: 'icon', type: 'image/png', sizes: '32x32', href: `${base}favicon-32.png` }],
    ['link', { rel: 'icon', type: 'image/png', sizes: '16x16', href: `${base}favicon-16.png` }],
    ['link', { rel: 'apple-touch-icon', sizes: '180x180', href: `${base}apple-touch-icon.png` }],
    ['link', { rel: 'manifest', href: `${base}site.webmanifest` }],
    ['meta', { name: 'theme-color', content: '#e11d48' }],
  ],

  sitemap: {
    hostname: siteUrl,
  },

  transformHead({ pageData, siteConfig }) {
    return seoHead(pageData, siteConfig.srcDir, pageData.relativePath.startsWith('sk/') ? descriptionSk : description)
  },

  // English is the default locale at the site root; Slovak mirrors every page under /sk/.
  locales: {
    root: {
      label: 'English',
      lang: 'en-US',
      description,
    },
    sk: {
      label: 'Slovenčina',
      lang: 'sk-SK',
      link: '/sk/',
      description: descriptionSk,
      themeConfig: {
        nav: navSk,
        sidebar: sidebarSk,
        outline: { level: [2, 3], label: 'Na tejto stránke' },
        docFooter: {
          prev: 'Predchádzajúca stránka',
          next: 'Nasledujúca stránka',
        },
        editLink: {
          pattern: `${repo}/edit/master/docs/:path`,
          text: 'Upraviť túto stránku na GitHube',
        },
        lastUpdated: {
          text: 'Naposledy upravené',
          formatOptions: { dateStyle: 'medium' },
        },
        darkModeSwitchLabel: 'Vzhľad',
        lightModeSwitchTitle: 'Prepnúť na svetlý režim',
        darkModeSwitchTitle: 'Prepnúť na tmavý režim',
        sidebarMenuLabel: 'Ponuka',
        returnToTopLabel: 'Späť na začiatok',
        langMenuLabel: 'Zmeniť jazyk',
        skipToContentLabel: 'Preskočiť na obsah',
        footer: {
          message: 'Vydané pod <a href="https://github.com/tomaj/invoice-client/blob/master/LICENSE.md">licenciou MIT</a>. Nie je prepojené so službou chcemfakturu.sk.',
          copyright: 'Copyright © 2015-2026 Tomas Majer',
        },
        notFound: {
          code: '404',
          title: 'STRÁNKA SA NENAŠLA',
          quote: 'Táto stránka neexistuje alebo sa presunula. Skúste vyhľadávanie alebo začnite od úvodu.',
          linkLabel: 'prejsť na úvod',
          linkText: 'Späť na úvod',
        },
      },
    },
  },

  themeConfig: {
    logo: { light: '/logo.svg', dark: '/logo-dark.svg', alt: 'Invoice Client' },
    siteTitle: 'Invoice Client',

    nav,

    sidebar,

    outline: [2, 3],

    socialLinks: [
      { icon: 'github', link: repo },
    ],

    editLink: {
      pattern: `${repo}/edit/master/docs/:path`,
      text: 'Edit this page on GitHub',
    },

    search: {
      provider: 'local',
      options: {
        locales: {
          sk: {
            translations: {
              button: {
                buttonText: 'Hľadať',
                buttonAriaLabel: 'Hľadať',
              },
              modal: {
                displayDetails: 'Zobraziť podrobný zoznam',
                resetButtonTitle: 'Vymazať hľadanie',
                backButtonTitle: 'Zavrieť hľadanie',
                noResultsText: 'Žiadne výsledky pre',
                footer: {
                  selectText: 'vybrať',
                  selectKeyAriaLabel: 'enter',
                  navigateText: 'prejsť',
                  navigateUpKeyAriaLabel: 'šípka hore',
                  navigateDownKeyAriaLabel: 'šípka dole',
                  closeText: 'zavrieť',
                  closeKeyAriaLabel: 'escape',
                },
              },
            },
          },
        },
      },
    },

    footer: {
      message: 'Released under the <a href="https://github.com/tomaj/invoice-client/blob/master/LICENSE.md">MIT License</a>. Not affiliated with chcemfakturu.sk.',
      copyright: 'Copyright © 2015-2026 Tomas Majer',
    },
  },
})
