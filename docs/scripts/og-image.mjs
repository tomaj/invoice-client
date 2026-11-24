// Builds the raster images of the site from the SVG sources in public/:
//   og.png (1200 × 630 social card: logo, name, tagline and the hero illustration),
//   favicon-16.png, favicon-32.png, apple-touch-icon.png, icon-192.png, icon-512.png and favicon.ico.
// Needs Chrome or Chromium: npm run images (CHROME_PATH=/path/to/chrome when it is not in the default place).
import { readFileSync, writeFileSync } from 'node:fs'
import puppeteer from 'puppeteer-core'

const pub = (path) => new URL(`../public/${path}`, import.meta.url)
const read = (path) => readFileSync(pub(path), 'utf8')
const defaultChrome = process.platform === 'darwin'
  ? '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome'
  : '/usr/bin/google-chrome'

const browser = await puppeteer.launch({ executablePath: process.env.CHROME_PATH || defaultChrome, args: ['--no-sandbox'] })
const page = await browser.newPage()

// Social card
const logo = read('logo.svg').replace('<svg ', '<svg width="128" height="128" ')
const hero = read('illustrations/hero-light.svg').replace(/width="480" height="420"/, 'width="528" height="462"')
await page.setViewport({ width: 1200, height: 630 })
await page.setContent(`<html><body style="margin:0;width:1200px;height:630px;background:linear-gradient(135deg,#ffffff 0%,#fff1f2 100%);font-family:Inter,Helvetica,Arial,sans-serif;display:flex;align-items:center;justify-content:space-between;padding:0 56px 0 72px;box-sizing:border-box">
  <div style="max-width:560px">
    ${logo}
    <div style="font-size:76px;font-weight:800;color:#1e293b;line-height:1.05;margin-top:24px">Invoice Client</div>
    <div style="font-size:30px;color:#475569;margin-top:18px;line-height:1.35">Create, fetch and delete invoices through the chcemfakturu.sk API from PHP</div>
    <div style="font-size:24px;color:#be123c;margin-top:28px;font-weight:700">composer require tomaj/invoice-client</div>
  </div>
  ${hero}
</body></html>`)
await page.screenshot({ path: pub('og.png').pathname })
console.log('public/og.png written')

// Icons: the logo on a transparent background
const icon = read('logo.svg')
const pngs = {}
for (const [file, size] of [['favicon-16.png', 16], ['favicon-32.png', 32], ['favicon-48.png', 48], ['apple-touch-icon.png', 180], ['icon-192.png', 192], ['icon-512.png', 512]]) {
  await page.setViewport({ width: size, height: size })
  // the Apple touch icon gets a white background: iOS fills transparency with black
  const background = file === 'apple-touch-icon.png' ? '#ffffff' : 'transparent'
  const padding = file === 'apple-touch-icon.png' ? Math.round(size * 0.1) : 0
  await page.setContent(`<html><body style="margin:0;background:${background}">${icon.replace('<svg ', `<svg width="${size - 2 * padding}" height="${size - 2 * padding}" style="display:block;margin:${padding}px" `)}</body></html>`)
  pngs[file] = await page.screenshot({ omitBackground: background === 'transparent', type: 'png' })
  if (file !== 'favicon-48.png') {
    writeFileSync(pub(file), pngs[file])
    console.log(`public/${file} written`)
  }
}
await browser.close()

// favicon.ico with 16, 32 and 48 px PNG images (the ICO format allows PNG payloads)
const images = [['favicon-16.png', 16], ['favicon-32.png', 32], ['favicon-48.png', 48]]
const header = Buffer.alloc(6 + 16 * images.length)
header.writeUInt16LE(0, 0)
header.writeUInt16LE(1, 2)
header.writeUInt16LE(images.length, 4)
let offset = header.length
images.forEach(([file, size], i) => {
  const data = pngs[file]
  const entry = 6 + 16 * i
  header.writeUInt8(size, entry)
  header.writeUInt8(size, entry + 1)
  header.writeUInt8(0, entry + 2)
  header.writeUInt8(0, entry + 3)
  header.writeUInt16LE(1, entry + 4)
  header.writeUInt16LE(32, entry + 6)
  header.writeUInt32LE(data.length, entry + 8)
  header.writeUInt32LE(offset, entry + 12)
  offset += data.length
})
writeFileSync(pub('favicon.ico'), Buffer.concat([header, ...images.map(([file]) => pngs[file])]))
console.log('public/favicon.ico written')
