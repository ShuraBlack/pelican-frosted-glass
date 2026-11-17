<?php

namespace ShuraBlack\FrostedGlass;

use App\Contracts\Plugins\HasPluginSettings;
use App\Contracts\Plugins\HasTheme;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\Schemas\Components\Section;
use Filament\View\PanelsRenderHook;

class FrostedGlassTheme implements HasPluginSettings, HasTheme
{
    /** option => [label, background-size, background-repeat] */
    private const IMAGE_FITS = [
        'cover' => ['Fill screen', 'cover', 'no-repeat'],
        'contain' => ['Show whole image', 'contain', 'no-repeat'],
        'tile' => ['Tile', 'auto', 'repeat'],
    ];

    private const IMAGE_POSITIONS = [
        'center' => 'Center',
        'top' => 'Top',
        'bottom' => 'Bottom',
        'left' => 'Left',
        'right' => 'Right',
    ];

    public function getId(): string
    {
        return 'frosted-glass';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}

    public function getThemeColors(): array
    {
        return [];
    }

    public function getThemeFont(): ?string
    {
        return null;
    }

    // No Vite entry: the CSS is inlined, so editing theme.css needs no asset rebuild.
    public function getThemeViteEntry(): string|array|null
    {
        return null;
    }

    public function getThemeRenderHooks(): array
    {
        return [
            PanelsRenderHook::HEAD_END => fn (): string => '<style id="frosted-glass">' . $this->buildCss() . '</style>',
        ];
    }

    public function buildCss(): string
    {
        $s = $this->getSettingsFormData();
        $v = fn (string $key) => filled($s[$key] ?? null) ? (string) $s[$key] : null;
        $image = $v('background_image');

        // Lookups, not raw values: only known CSS keywords reach the stylesheet.
        $fit = self::IMAGE_FITS[$v('image_fit') ?? ''] ?? null;
        $position = array_key_exists($v('image_position') ?? '', self::IMAGE_POSITIONS) ? $v('image_position') : null;

        $vars = array_filter([
            '--glass-color' => $v('glass_color'),
            '--glass-stone' => $v('stone_color'),
            '--glass-image-size' => $fit[1] ?? null,
            '--glass-image-repeat' => $fit[2] ?? null,
            '--glass-image-position' => $position,
            '--glass-dim' => $v('image_dim') !== null ? (float) $v('image_dim') / 100 : null,
            '--glass-image' => $image ? 'url(' . json_encode($image, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ')' : null,
            '--glass-blur' => $v('blur') !== null ? (float) $v('blur') . 'px' : null,
            '--glass-tint' => $v('tint') !== null ? (float) $v('tint') / 100 : null,
            '--glass-radius' => $v('radius') !== null ? (float) $v('radius') . 'px' : null,
        ], fn ($value) => $value !== null);

        $css = (string) file_get_contents(plugin_path($this->getId(), 'resources/css/theme.css'));

        if ($vars) {
            // ":root.dark" listed too, so settings also win over the dark-mode defaults.
            $css .= ':root,:root.dark{' . implode(';', array_map(fn ($k, $val) => "$k:$val", array_keys($vars), $vars)) . '}';
        }

        // Keep the CSS from closing the <style> tag and injecting HTML.
        return str_replace('</', '<\/', $css);
    }

    public function getSettingsFormData(): array
    {
        $path = self::settingsPath();

        return is_file($path) ? (json_decode((string) file_get_contents($path), true) ?? []) : [];
    }

    public function getSettingsForm(): array
    {
        return [
            Section::make('Glass')->schema([
                ColorPicker::make('glass_color')->label('Milk color')
                    ->helperText('Color the glass is tinted with. Empty = white.'),
                TextInput::make('tint')->label('Milk strength')->numeric()->minValue(0)->maxValue(100)->suffix('%')
                    ->helperText('How milky the glass is. Empty = 30% in light mode, 8% in dark mode.'),
                TextInput::make('blur')->label('Blur')->numeric()->minValue(0)->maxValue(40)->suffix('px')->placeholder('16'),
                TextInput::make('radius')->label('Corner radius')->numeric()->minValue(0)->suffix('px')->placeholder('18'),
            ]),
            Section::make('Background')->schema([
                ColorPicker::make('stone_color')->label('Background color')
                    ->helperText('Empty = granite (lighter in light mode).'),
                TextInput::make('background_image')->label('Background image URL')->url()
                    ->helperText('The glass effect shows best on an image.'),
                Select::make('image_fit')->label('Image fit')->options(array_map(fn ($fit) => $fit[0], self::IMAGE_FITS))->placeholder('Fill screen'),
                Select::make('image_position')->label('Image position')->options(self::IMAGE_POSITIONS)->placeholder('Center'),
                TextInput::make('image_dim')->label('Image overlay')->numeric()->minValue(0)->maxValue(100)->suffix('%')->placeholder('0')
                    ->helperText('Fades the image into the background color, e.g. to darken a bright image.'),
            ]),
        ];
    }

    public function saveSettings(array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // The panel swallows exceptions from saveSettings, so report failure ourselves.
        if (@file_put_contents(self::settingsPath(), $json) === false) {
            Notification::make()->title('Could not write ' . self::settingsPath())->danger()->send();

            return;
        }

        Notification::make()->title('Theme settings saved')->success()->send();
    }

    private static function settingsPath(): string
    {
        return storage_path('app/frosted-glass.json');
    }
}
