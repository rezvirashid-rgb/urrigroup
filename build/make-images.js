/**
 * URRI GROUP — regenerate icons and the social share image.
 *
 *   node build/make-images.js
 *
 * Only needed when the logo or share image changes (outputs are committed).
 * Requires Node + Playwright (Chromium). Renders from the real SVG logo and
 * the self-hosted fonts, writing into src/static/.
 */
const path = require("path");
const fs = require("fs");
let chromium;
try { ({ chromium } = require("playwright")); }
catch { ({ chromium } = require("/opt/node22/lib/node_modules/playwright")); }

const ROOT = path.resolve(__dirname, "..");
const STATIC = path.join(ROOT, "src/static");
const ICONS = path.join(STATIC, "assets/icons");
const font = (f) => "data:font/woff2;base64," + fs.readFileSync(path.join(STATIC, "assets/fonts", f)).toString("base64");
const LOGO = fs.readFileSync(path.join(ICONS, "favicon.svg"), "utf8");

/** Build a .ico that embeds PNG images (supported by all current browsers). */
function makeIco(pngs) {
  const header = Buffer.alloc(6 + 16 * pngs.length);
  header.writeUInt16LE(0, 0); header.writeUInt16LE(1, 2); header.writeUInt16LE(pngs.length, 4);
  let offset = header.length;
  pngs.forEach(({ size, buf }, i) => {
    const e = 6 + i * 16;
    header.writeUInt8(size >= 256 ? 0 : size, e);
    header.writeUInt8(size >= 256 ? 0 : size, e + 1);
    header.writeUInt16LE(1, e + 4);      // colour planes
    header.writeUInt16LE(32, e + 6);     // bits per pixel
    header.writeUInt32LE(buf.length, e + 8);
    header.writeUInt32LE(offset, e + 12);
    offset += buf.length;
  });
  return Buffer.concat([header, ...pngs.map(p => p.buf)]);
}

const ogHtml = `<!doctype html><html><head><style>
@font-face{font-family:Anton;src:url(${font("anton-400.woff2")})}
@font-face{font-family:Poppins;font-weight:500;src:url(${font("poppins-500.woff2")})}
html,body{margin:0;width:1200px;height:630px;overflow:hidden}
body{position:relative;color:#fff;font-family:Poppins;
  background:radial-gradient(900px 520px at 92% -10%,rgba(73,2,188,.75),transparent 60%),
             radial-gradient(700px 420px at -5% 115%,rgba(249,113,113,.28),transparent 55%),
             linear-gradient(160deg,#150c2b 0%,#120B24 55%,#1a0f38 100%)}
body:after{content:"";position:absolute;inset:0;opacity:.55;
  background-image:repeating-linear-gradient(115deg,rgba(255,255,255,.05) 0 1px,transparent 1px 46px);
  -webkit-mask-image:linear-gradient(90deg,transparent,#000 45%)}
.wrap{position:relative;z-index:1;padding:72px 84px}
.brand{display:flex;align-items:center;gap:16px;font-family:Anton;font-size:40px;letter-spacing:1.5px}
.brand svg{width:58px;height:58px}.brand span{color:#F97171}
h1{font-family:Anton;font-weight:400;text-transform:uppercase;font-size:104px;line-height:.95;margin:58px 0 26px;letter-spacing:1px}
h1 em{font-style:normal;color:#FFD319}
p{font-size:28px;color:rgba(255,255,255,.8);margin:0}
</style></head><body><div class="wrap">
<div class="brand">${LOGO}URRI <span>GROUP</span></div>
<h1>One Group.<br>Every Direction <em>Forward.</em></h1>
<p>A connected family of Bangladeshi businesses.</p>
</div></body></html>`;

(async () => {
  const exe = ["/opt/pw-browsers/chromium-1194/chrome-linux/chrome"].find(p => fs.existsSync(p));
  const browser = await chromium.launch(exe ? { executablePath: exe } : {});
  const page = await browser.newPage();

  const iconPng = async (size) => {
    await page.setViewportSize({ width: size, height: size });
    await page.setContent(`<html><body style="margin:0;background:transparent">${LOGO.replace("<svg ", `<svg width="${size}" height="${size}" `)}</body></html>`);
    return page.screenshot({ omitBackground: true, clip: { x: 0, y: 0, width: size, height: size } });
  };

  for (const [name, size] of [["apple-touch-icon.png", 180], ["icon-192.png", 192], ["icon-512.png", 512]]) {
    fs.writeFileSync(path.join(ICONS, name), await iconPng(size));
  }
  const icoParts = [];
  for (const s of [16, 32, 48]) icoParts.push({ size: s, buf: await iconPng(s) });
  fs.writeFileSync(path.join(STATIC, "favicon.ico"), makeIco(icoParts));

  await page.setViewportSize({ width: 1200, height: 630 });
  await page.setContent(ogHtml, { waitUntil: "load" });
  await page.evaluate(() => document.fonts.ready);
  fs.mkdirSync(path.join(STATIC, "assets/images"), { recursive: true });
  fs.writeFileSync(path.join(STATIC, "assets/images/og-image.jpg"),
    await page.screenshot({ type: "jpeg", quality: 85 }));

  await browser.close();
  console.log("Icons and share image written to src/static/");
})().catch(e => { console.error(e); process.exit(1); });
