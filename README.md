# Frosted Glass

A Pelican theme with frosted glass surfaces: translucent, blurred cards over a granite background or your own image. Works in light and dark mode.

## Settings

Open **Admin → Plugins → Frosted Glass → Settings**.

### Glass

| Setting | Description |
|---|---|
| Milk color | Color the glass is tinted with. Default: white. |
| Milk strength | How milky the glass is, 0–100 %. Default: 30 % in light mode, 8 % in dark mode. |
| Blur | Blur behind the glass, in px. Default: 16. |
| Corner radius | In px. Default: 18. |

### Background

| Setting | Description |
|---|---|
| Background color | Default: granite (`#3b3d40` dark, `#8e9195` light). |
| Background image URL | Direct link to an image. The glass effect shows best on an image. |
| Image fit | Fill screen, show whole image, or tile. |
| Image position | Center, top, bottom, left or right. |
| Image overlay | 0–100 %. Fades the image into the background color, e.g. to darken it. |

## Customizing the CSS

The styles live in `resources/css/theme.css`. They are inlined on every page load, so edits apply on the next reload with no rebuild. The `--glass-*` variables at the top control the whole look.

Glass is applied to the login card, topbar, sidebar, sections, tables, modals, dropdowns and the footer.

## Notes

- Settings are stored in the panel's `.env` as `FROSTED_GLASS_*`, so plugin updates keep them.
- The milk color uses CSS `color-mix()`, which needs a browser from 2023 or later.
- Many blurred surfaces over a fixed background can cost some scroll performance on weak devices.
- If the Login Styler plugin sets a login background, it overrides the theme on the login page.
