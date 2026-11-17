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

Glass is applied to the login card, topbar, sidebar, sections, tables, stat blocks (e.g. name, status and address on the server page), modals, dropdowns, the footer and the server console (dark glass in both modes, since console text is light). Scrollbars are thin and translucent to match.

In the server list, each server's image fills its whole card and fades in from the left. Set `--glass-server-image-opacity` (default `0.35`) in `theme.css` to make it stronger or weaker.

Icon previews (egg and server edit pages, server settings, icon upload) use a 16:9 box and show the whole image, so wide images are no longer squashed.

## Notes

- Settings are stored in the panel's `.env` as `FROSTED_GLASS_*`, so plugin updates keep them.
- The milk color uses CSS `color-mix()`, which needs a browser from 2023 or later.
- Many blurred surfaces over a fixed background can cost some scroll performance on weak devices. On screens up to 768 px wide the blur is halved.
- If transparency effects are turned off in the operating system (`prefers-reduced-transparency`), or the browser lacks `backdrop-filter`, surfaces turn near-solid.
- In dark mode, text on glass gets a soft shadow so it stays readable over bright images.
- If the Login Styler plugin sets a login background, it overrides the theme on the login page.
