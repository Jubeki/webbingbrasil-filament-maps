@php
    use Filament\Support\Enums\Size;
    use Filament\Support\Enums\Size;
@endphp

@props([
    'badge' => null,
    'badgeColor' => 'primary',
    'badgeSize' => 'xs',
    'color' => 'primary',
    'disabled' => false,
    'form' => null,
    'formId' => null,
    'href' => null,
    'icon' => null,
    'iconAlias' => null,
    'size' => null,
    'keyBindings' => null,
    'label' => null,
    'loadingIndicator' => true,
    'size' => Size::Medium,
    'spaMode' => null,
    'tag' => 'button',
    'target' => null,
    'tooltip' => null,
    'type' => 'button',
])

@php
    if (! $size instanceof Size) {
        $size = filled($size) ? (Size::tryFrom($size) ?? $size) : null;
    }

    $size ??= match ($size) {
        Size::ExtraSmall => Size::Small,
        Size::Small, Size::Medium => Size::Medium,
        Size::Large, Size::ExtraLarge => Size::Large,
        default => Size::Medium,
    };

    if (! $size instanceof Size) {
        $size = filled($size) ? (Size::tryFrom($size) ?? $size) : null;
    }

    $buttonClasses = \Illuminate\Support\Arr::toCssClasses([
        ...[
            'fi-icon-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 shadow-sm rounded-lg',
            'pointer-events-none opacity-70' => $disabled,
            'cursor-pointer' => $tag === 'label',
            match ($color) {
                'gray' => null,
                default => 'fi-color-custom',
            },
            is_string($color) ? "fi-color-{$color}" : null,
            ($size instanceof Size) ? "fi-size-{$size->value}" : null,
            match ($size) {
                Size::ExtraSmall => 'gap-1 px-2 py-1.5 text-xs',
                Size::Small => 'gap-1 px-2.5 py-1.5 text-sm',
                Size::Medium => 'gap-1.5 px-3 py-2 text-sm',
                Size::Large => 'gap-1.5 px-3.5 py-2.5 text-sm',
                Size::ExtraLarge => 'gap-1.5 px-4 py-3 text-sm',
                default => $size,
            },
            'bg-white text-gray-950 hover:bg-gray-50 dark:bg-white/5 dark:text-white dark:hover:bg-white/10' => ($color === 'gray') || ($tag === 'label'),
            'ring-1 ring-gray-950/10 dark:ring-white/20' => (($color === 'gray') || ($tag === 'label')),
            'bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50' => ($color !== 'gray') && ($tag !== 'label'),
            '[input:checked+&]:bg-custom-600 [input:checked+&]:text-white [input:checked+&]:ring-0 [input:checked+&]:hover:bg-custom-500 dark:[input:checked+&]:bg-custom-500 dark:[input:checked+&]:hover:bg-custom-400 [input:checked:focus-visible+&]:ring-custom-500/50 dark:[input:checked:focus-visible+&]:ring-custom-400/50 [input:focus-visible+&]:z-10 [input:focus-visible+&]:ring-2 [input:focus-visible+&]:ring-gray-950/10 dark:[input:focus-visible+&]:ring-white/20' => ($color !== 'gray') && ($tag === 'label'),
            '[input:checked+&]:bg-gray-400 [input:checked+&]:text-white [input:checked+&]:ring-0 [input:checked+&]:hover:bg-gray-300 dark:[input:checked+&]:bg-gray-600 dark:[input:checked+&]:hover:bg-gray-500' => ($color === 'gray'),
        ],
    ]);

    $buttonStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $color,
            shades: [400, 500, 600],
            alias: 'button',
        ) => $color !== 'gray',
    ]);

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-icon-btn-icon transition duration-75',
        match ($size) {
            Size::Small => 'h-4 w-4',
            Size::Medium => 'h-5 w-5',
            Size::Large => 'h-6 w-6',
            default => $size,
        },
        'text-gray-400 dark:text-gray-500' => ($color === 'gray') || ($tag === 'label'),
        'text-white' => ($color !== 'gray') && ($tag !== 'label'),
        '[:checked+*>&]:text-white' => $tag === 'label',
    ]);

    $badgeContainerClasses = 'fi-icon-btn-badge-ctn absolute start-full top-1 z-[1] w-max -translate-x-1/2 -translate-y-1/2 rounded-md bg-white dark:bg-gray-900 rtl:translate-x-1/2';

    $wireTarget = $loadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

    $hasLoadingIndicator = filled($wireTarget) || ($type === 'submit' && filled($form));

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
    }

    $hasTooltip = filled($tooltip);
@endphp

@if ($tag === 'button')
    <button
        @if ($keyBindings || $hasTooltip)
            x-data="{}"
        @endif
        @if ($keyBindings)
            x-bind:id="$id('key-bindings')"
            x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}="document.getElementById($el.id).click()"
        @endif
        @if ($hasTooltip)
            x-tooltip="{
                content: @js($tooltip),
                theme: $store.theme,
            }"
        @endif
        {{
            $attributes
                ->merge([
                    'disabled' => $disabled,
                    'form' => $formId,
                    'type' => $type,
                    'wire:loading.attr' => 'disabled',
                    'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
                ], escape: false)
                ->merge([
                    'title' => $hasTooltip ? null : $label,
                ], escape: true)
                ->class([$buttonClasses])
                ->style([$buttonStyles])
        }}
    >
        @if ($label)
            <span class="sr-only">
                {{ $label }}
            </span>
        @endif

        <x-filament::icon
            :attributes="
                \Filament\Support\prepare_inherited_attributes(
                    new \Illuminate\View\ComponentAttributeBag([
                        'alias' => $iconAlias,
                        'icon' => $icon,
                        'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
                        'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : null,
                    ])
                )->class([$iconClasses])
            "
        />

        @if ($hasLoadingIndicator)
            <x-filament::loading-indicator
                :attributes="
                    \Filament\Support\prepare_inherited_attributes(
                        new \Illuminate\View\ComponentAttributeBag([
                            'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                            'wire:target' => $loadingIndicatorTarget,
                        ])
                    )->class([$iconClasses])
                "
            />
        @endif

        @if (filled($badge))
            <div class="{{ $badgeContainerClasses }}">
                <x-filament::badge :color="$badgeColor" :size="$badgeSize">
                    {{ $badge }}
                </x-filament::badge>
            </div>
        @endif
    </button>
@elseif ($tag === 'a')
    <a
        {{ \Filament\Support\generate_href_html($href, $target === '_blank', $spaMode) }}
        @if ($keyBindings || $hasTooltip)
            x-data="{}"
        @endif
        @if ($keyBindings)
            x-bind:id="$id('key-bindings')"
            x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}="document.getElementById($el.id).click()"
        @endif
        @if ($hasTooltip)
            x-tooltip="{
                content: @js($tooltip),
                theme: $store.theme,
            }"
        @endif
        {{
            $attributes
                ->merge([
                    'title' => $hasTooltip ? null : $label,
                ], escape: true)
                ->class([$buttonClasses])
                ->style([$buttonStyles])
        }}
    >
        @if ($label)
            <span class="sr-only">
                {{ $label }}
            </span>
        @endif

        <x-filament::icon
            :alias="$iconAlias"
            :icon="$icon"
            :class="$iconClasses"
        />

        @if (filled($badge))
            <div class="{{ $badgeContainerClasses }}">
                <x-filament::badge :color="$badgeColor" size="xs">
                    {{ $badge }}
                </x-filament::badge>
            </div>
        @endif
    </a>
@endif
