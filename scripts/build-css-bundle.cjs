/**
 * دمج وضغط ملفات CSS للقالب في ملف واحد للإنتاج.
 * التشغيل: npm run build:css
 * الناتج: public/assets/css/vendor.bundle.rtl.min.css
 */
const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const root = path.resolve(__dirname, '..');
const publicAssets = path.join(root, 'public', 'assets');
const outDir = path.join(publicAssets, 'css');
const outFile = path.join(outDir, 'vendor.bundle.rtl.min.css');

const files = [
  'plugins/global/plugins.bundle.rtl.css',
  'plugins/custom/prismjs/prismjs.bundle.rtl.css',
  'css/style.bundle.rtl.css',
  'css/themes/layout/header/base/light.rtl.css',
  'css/themes/layout/header/menu/light.rtl.css',
  'css/themes/layout/brand/dark.rtl.css',
  'css/themes/layout/aside/dark.rtl.css',
].map((f) => path.join(publicAssets, f));

const missing = files.filter((f) => !fs.existsSync(f));
if (missing.length) {
  console.error('ملفات غير موجودة:', missing.map((f) => path.relative(root, f)).join(', '));
  process.exit(1);
}

if (!fs.existsSync(outDir)) {
  fs.mkdirSync(outDir, { recursive: true });
}

try {
  execSync(
    `npx clean-css-cli -o "${outFile}" ${files.map((f) => `"${f}"`).join(' ')}`,
    { stdio: 'inherit', cwd: root, shell: true }
  );
  console.log('تم إنشاء الملف:', path.relative(root, outFile));
} catch (e) {
  process.exit(e.status || 1);
}
