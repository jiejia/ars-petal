# ARS Petal

[English](README.md) | 简体中文

一款明亮的组件库风格 WordPress 播客主题：大量留白、粗细混排的大标题、彩色色块与柔和光晕，并带有蓝灰色夜间模式。搭配 [A Ripple Song](https://github.com/jiejia/a-ripple-song) 插件即可成为完整的播客站点，单独使用则是一款简洁的博客主题。

<table>
  <tr>
    <td colspan="2"><img src="docs/screenshots/home-light.png" alt="ARS Petal 首页（日间模式），全站播放器正在播放" /></td>
  </tr>
  <tr>
    <td width="70%"><img src="docs/screenshots/home-dark.png" alt="ARS Petal 首页（夜间模式）" /></td>
    <td width="30%"><img src="docs/screenshots/home-mobile.png" alt="ARS Petal 移动端首页，播放器已展开" /></td>
  </tr>
</table>

在线演示：[podcast.aripplesong.com](https://podcast.aripplesong.com/)

## 功能特性

插件负责播客数据、RSS Feed 和全站播放器，主题负责呈现。

- **落地页**：由 `Home Top` 小工具区域构建的页面模板，内置精选 / 最新节目轮播、主播与嘉宾、节目横向列表与列表、统计数据和栏目卡片等小工具。
- **节目页面**：归档页带栏目侧边栏、排序和列表 / 网格视图；详情页提供播放、分享、喜欢、下载操作，以及简介 / 章节 / 文字稿标签页。
- **播放不中断**：基于 Swup 的页面过渡让播放器持续播放，节目卡片同步显示“正在播放”状态。
- **配色模式**：日间、夜间、跟随系统，访客可自行切换。
- **导航**：页头 / 页脚菜单、大型下拉面板、移动端抽屉导航，可通过 `icon-{name}` 类为菜单项添加 Lucide 图标。
- **更多**：图片或内置图标 Logo、搜索弹窗、公告栏、订阅区块。
- **支持翻译**，内置英文与简体中文。

## 安装

运行环境：WordPress 6.6+，PHP 8.2+。

1. 从最新的 [Release](https://github.com/jiejia/ars-petal/releases) 下载 `ars-petal.zip`，在 `外观 > 主题 > 安装主题 > 上传主题` 中上传并启用。
2. 可选：安装并启用 [A Ripple Song](https://github.com/jiejia/a-ripple-song)，主题会自动选用对应的 `ars-petal` 播放器皮肤。
3. 想把落地页设为首页：点击启用主题后通知里的 `创建并设为首页`（或新建一个使用 `Landing page` 模板的页面，在 `设置 > 阅读` 中设为静态首页）。
4. 在 `外观 > 小工具 > Home Top` 中编排落地页（为空时显示默认板块），在 `外观 > 自定义 > Petal` 中设置配色和页脚文字。

## 开发

```bash
npm install
npm run build              # 构建图标、CSS、JS 到 public/dist
npm run watch:css          # 或 watch:js
npm run i18n               # 重新生成翻译文件（需要 WP-CLI）
npm run package            # 打包 build/ars-petal.zip
npm run release X.Y.Z      # 更新版本号、提交、打标签并推送，由 GitHub Actions 发布
```

技术栈：[Tailwind CSS](https://tailwindcss.com/)、[daisyUI](https://daisyui.com/)、[Alpine.js](https://alpinejs.dev/)、[Swup](https://swup.js.org/)、[esbuild](https://esbuild.github.io/)、[Lucide](https://lucide.dev/)。

| 目录 | 内容 |
| --- | --- |
| `app/` | 主题逻辑：初始化、资源、定制器、导航、小工具、A Ripple Song 集成 |
| `resources/templates/` | PHP 模板：布局、首页板块、卡片、局部模板、节目视图 |
| `resources/assets/` | CSS、JS、图标、图片 |
| `resources/lang/` | 翻译文件 |
| `bin/` | 构建、打包、国际化和发布脚本 |

## 许可证

[GPL-3.0](LICENSE) · 官网：[aripplesong.com](https://aripplesong.com/)
