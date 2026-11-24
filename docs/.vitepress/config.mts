import { defineConfig } from 'vitepress'
import { seoHead, siteUrl } from './seo'

const base = process.env.DOCS_BASE || '/invoice-client/'
const description = 'PHP client for the chcemfakturu.sk invoicing API: build typed invoices, create, fetch and delete them, handle every error as a response, log through PSR-3 and test without the network'
const repo = 'https://github.com/tomaj/invoice-client'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: 'Invoice Client',
  description,
  lang: 'en',
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
    return seoHead(pageData, siteConfig.srcDir, description)
  },

  themeConfig: {
    logo: { light: '/logo.svg', dark: '/logo-dark.svg', alt: 'Invoice Client' },
    siteTitle: 'Invoice Client',

    nav: [
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
    ],

    sidebar: [
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
    ],

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
    },

    footer: {
      message: 'Released under the <a href="https://github.com/tomaj/invoice-client/blob/master/LICENSE.md">MIT License</a>. Not affiliated with chcemfakturu.sk.',
      copyright: 'Copyright © 2015-2026 Tomas Majer',
    },
  },
})
