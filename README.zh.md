# ARS Petal

ARS Petal 是一款明亮的组件库风格 WordPress 播客主题，粗细混排的大标题、彩色色块、柔和光晕，并带有蓝灰色夜间模式。搭配 [A Ripple Song](https://github.com/jiejia/a-ripple-song) 插件即可成为完整的播客站点；单独使用时是一款简洁的博客主题。

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

## 概览

A Ripple Song 插件负责播客数据、RSS Feed 和全站播放器，主题负责把它们呈现为播客首页、节目归档页和节目详情页。

## 功能特性

- `Landing page`（落地页）模板由 `Home Top` 小工具区域构建，内置轮播、主播与嘉宾、节目列表、统计数据和栏目卡片等小工具。
- 节目归档页带栏目侧边栏、排序，以及列表 / 网格视图。
- 节目详情页提供播放、分享、喜欢、下载操作，以及简介 / 章节 / 文字稿标签页。
- 基于 Swup 的页面过渡，浏览时播放不中断。
- 日间、夜间、跟随系统三种配色模式。
- 页头 / 页脚菜单、大型下拉面板、移动端抽屉导航，可通过 `icon-{name}` 类添加 Lucide 菜单图标。
- 图片或内置图标 Logo、搜索弹窗、公告栏和订阅区块。
- 支持翻译，内置英文与简体中文。

## 配套插件

- [A Ripple Song](https://github.com/jiejia/a-ripple-song)：开启播客功能。启用主题后会自动选用 `ars-petal` 播放器皮肤。

## 安装

### 发布包

1. 从最新的 [GitHub Release](https://github.com/jiejia/ars-petal/releases) 下载 `ars-petal.zip`。
2. 在 WordPress 后台进入 `外观 > 主题 > 安装主题 > 上传主题`，上传 zip 并启用。
3. 点击启用后通知里的 `创建并设为首页`，把落地页设为首页。
4. 在 `外观 > 小工具 > Home Top` 中编排落地页，在 `外观 > 自定义 > Petal` 中设置配色和页脚文字。

### 运行环境

- WordPress `6.6+`
- PHP `8.2+`

## 开发

```bash
npm install
```

构建生产资源：

```bash
npm run build
```

监听 CSS 或 JS：

```bash
npm run watch:css
npm run watch:js
```

重新生成翻译文件（需要 WP-CLI）：

```bash
npm run i18n
```

打包主题 zip，或发布新版本：

```bash
npm run package
npm run release X.Y.Z
```

## 技术栈

- [Tailwind CSS](https://tailwindcss.com/)
- [daisyUI](https://daisyui.com/)
- [Alpine.js](https://alpinejs.dev/)
- [Swup](https://swup.js.org/)
- [esbuild](https://esbuild.github.io/)
- [Lucide](https://lucide.dev/)

## 项目结构

- `app/`：主题逻辑，包括初始化、资源、定制器、导航、小工具和插件集成。
- `resources/templates/`：PHP 模板，包括布局、板块、卡片和节目视图。
- `resources/assets/`：CSS、JS、图标和图片。
- `resources/lang/`：翻译文件。
- `bin/`：构建、打包、国际化和发布脚本。

## 链接

- 代码仓库：[github.com/jiejia/ars-petal](https://github.com/jiejia/ars-petal)
- 演示站点：[podcast.aripplesong.com](https://podcast.aripplesong.com/)
- 官方网站：[aripplesong.com](https://aripplesong.com/)

## 语言

- [English](README.md)
- [简体中文](README.zh.md)

## 许可证

基于 [GNU General Public License v3.0](LICENSE) 发布。
