@php
$collapsible = $this->isCollapsible();
@endphp


<x-filament-widgets::widget class="fi-wi-table">
    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Widgets\View\WidgetsRenderHook::TABLE_WIDGET_START, scopes: static::class) }}

    <x-filament::section 
        :collapsible="$collapsible" 
    >

    {{ $this->table }}

    </x-filament::section>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Widgets\View\WidgetsRenderHook::TABLE_WIDGET_END, scopes: static::class) }}
</x-filament-widgets::widget>
