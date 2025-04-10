<?php

namespace App\Filament\Widgets;

use Exception;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Provinvoice;
use Illuminate\Support\Str;
use App\Models\ProvTranches;
//use Filament\Widgets\TableWidget;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Facades\FilamentColor;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

FilamentColor::register([
    'danger2' => Color::hex('#b0347f'), //purple
]);

class ProvisionSummary extends BaseWidget
{
    protected static ?string $heading = 'Total Provision (Productos)';
    protected static ?int $sort = 3;

    protected $listeners = ['updateProvisionSumary' => '$refresh'];
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    //public string $queryse;

    public function table(Table $table): Table
    {
        $queryse = $this->get_session_values();
        error_log($queryse);

        return $table
            ->query(
                Provinvoice::query()
                ->select(DB::raw('
                    product
                    ,min(id) as id
                    ,count(*) as invoices
                    ,sum(actual_debt) as actual_debt
                    ,sum(provision) as provision
                    '
                    ))
                ->groupBy('product')
                ->orderBy('provision','desc') //mandatory for allow laravel to execute the query
                ->whereRaw("{$queryse}")
            )
            ->columns([
                // ...
                TextColumn::make('product')
                    ->grow(false),

                TextColumn::make('invoices')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0)
                    ->summarize(Sum::make()
                        ->label('') 
                        ->numeric(decimalPlaces: 0)
                        ),

                TextColumn::make('actual_debt')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0)
                    ->summarize(Sum::make()
                        ->label('') 
                        ->numeric(decimalPlaces: 0)
                        ),
                
                TextColumn::make('provision')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0)
                    ->summarize(Sum::make()
                        ->label('') 
                        ->numeric(decimalPlaces: 0)
                    ),
                
                TextColumn::make('perc_provision')
                    ->getStateUsing(function(Model $record) {
                        return $record->provision / $record->actual_debt;
                    })
                    ->grow(false)
                    ->numeric(decimalPlaces: 3),
                    
            ])
            ->filters([
                //
                SelectFilter::make('country_code')
                ->options(fn (): array => Provinvoice::query()->pluck('country_code','country_code')->all()),

                SelectFilter::make('curve_segment')
                ->options(fn (): array => Provinvoice::query()->pluck('curve_segment','curve_segment')->all()),
            ]);

    }

    public function updated(): void
    {
        error_log('updated');
        $queryse = $this->pass_queryfilters();
        
    }

    public function resetTableFiltersForm(): void
    {
        error_log('resetTableFiltersForm');
        $queryse = $this->pass_queryfilters();
    }

    public function removeTableFilters(): void
    {
        error_log('removeTableFilters');
        $queryse = $this->pass_queryfilters();
    }

    public function isTableLoaded(): bool
    {
        error_log('loaded');
        $queryse = $this->pass_queryfilters();
        return True;
    }

    //public function removeTableFilter(string $filterName, ?string $field = null, bool $isRemovingAllFilters = false): void
    //{
    //    error_log($filterName);
    //    $queryse = $this->pass_queryfilters();
    //}

    public function pass_queryfilters(){

        $country_code = $this->table->getLivewire()->tableFilters['country_code']['value'];
        $curve_segment = $this->table->getLivewire()->tableFilters['curve_segment']['value'];

        $queryse = '1<2';
        $queryse = $country_code ? "{$queryse} and country_code in ('{$country_code}')" : $queryse;
        $queryse = $curve_segment ? "{$queryse} and curve_segment in ('{$curve_segment}')" : $queryse;

        session()->put('queryse', $queryse);

        $this->dispatch('updateProvisionSumary');

        return $queryse;
    }

    public function get_session_values(){

        $queryse = session()->get('queryse');

        $queryse = $queryse ? $queryse : '1<2';

        return $queryse;
    }

    
}
