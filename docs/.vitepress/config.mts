import { defineConfig } from 'vitepress'

const description = 'PHP client for the chcemfakturu.sk invoicing API: create, fetch and delete invoices'
const siteUrl = 'https://tomaj.github.io/invoice-client/'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: 'Invoice Client',
  description,
  base: '/invoice-client/',
  lastUpdated: true,

  head: [
    ['link', { rel: 'icon', type: 'image/svg+xml', href: '/invoice-client/favicon.svg' }],
    ['meta', { name: 'theme-color', content: '#1d4ed8' }],
    ['meta', { property: 'og:type', content: 'website' }],
    ['meta', { property: 'og:title', content: 'Invoice Client - PHP client for chcemfakturu.sk' }],
    ['meta', { property: 'og:description', content: description }],
    ['meta', { property: 'og:url', content: siteUrl }],
    ['meta', { name: 'twitter:card', content: 'summary' }],
  ],

  sitemap: {
    hostname: siteUrl,
  },

  themeConfig: {
    logo: '/logo.svg',

    nav: [
      { text: 'Guide', link: '/guide/getting-started' },
      { text: 'API', link: '/api/reference' },
      {
        text: '1.x (next)',
        items: [
          { text: 'Changelog', link: '/changelog' },
          { text: 'Upgrade from dev-master', link: 'https://github.com/tomaj/invoice-client/blob/master/UPGRADE-1.0.md' },
          { text: 'Releases', link: 'https://github.com/tomaj/invoice-client/releases' },
        ],
      },
    ],

    sidebar: [
      {
        text: 'Guide',
        items: [
          { text: 'Getting Started', link: '/guide/getting-started' },
          { text: 'Building Invoices', link: '/guide/invoices' },
          { text: 'API Calls and Responses', link: '/guide/api' },
          { text: 'Transports and Testing', link: '/guide/transports' },
        ],
      },
      {
        text: 'Reference',
        items: [
          { text: 'API Reference', link: '/api/reference' },
          { text: 'FAQ', link: '/faq' },
          { text: 'Changelog', link: '/changelog' },
          { text: 'Contributing', link: '/contributing' },
        ],
      },
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/tomaj/invoice-client' },
    ],

    editLink: {
      pattern: 'https://github.com/tomaj/invoice-client/edit/master/docs/:path',
      text: 'Edit this page on GitHub',
    },

    search: {
      provider: 'local',
    },

    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright © 2015-2026 Tomas Majer',
    },
  },
})
