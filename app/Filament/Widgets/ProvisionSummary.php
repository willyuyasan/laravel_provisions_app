<?php

namespace App\Filament\Widgets;

use Exception;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Provinvoice;
//use Filament\Widgets\TableWidget;
use Illuminate\Support\Str;
use App\Models\ProvTranches;
use Filament\Widgets\TableWidget;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Livewire;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Facades\FilamentColor;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Actions\Contracts\HasTable;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use App\Traits\Myutils;

FilamentColor::register([
    'danger2' => Color::hex('#b0347f'), //purple
]);

class ProvisionSummary extends BaseWidget
//class ProvisionSummary extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    use MyUtils;

    protected static ?string $heading = 'Total Provision (Productos)';
    protected static ?int $sort = 3;

    protected $listeners = ['updateProvisionSumary' => '$refresh'];
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    //public string $queryse;

    public function table(Table $table): Table
    {
        $queryse = $this->get_session_values();
        $prov_info = $this->provision_info();
        error_log($queryse);

        return $table
            ->heading('Total Provision (Productos)')
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
                //->whereRaw("{$queryse}")
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
                        $pp_ap = number_format(($record->provision / $record->actual_debt)*100, 2);
                        return $pp_ap;
                    })
                    ->grow(false)
                    ->numeric(decimalPlaces: 3)
                    ->summarize([
                        Summarizer::make()
                            ->using(fn () => $prov_info['pp_ap'])
                            ->numeric(decimalPlaces: 3),
                    ]),
            ])
            ->filters([
                //
                SelectFilter::make('country_code')
                ->options(fn (): array => Provinvoice::query()->pluck('country_code','country_code')->all()),

                SelectFilter::make('curve_segment')
                ->options(fn (): array => Provinvoice::query()->pluck('curve_segment','curve_segment')->all()),

                SelectFilter::make('product')
                ->options(fn (): array => Provinvoice::query()->pluck('product','product')->all()),
            ]);
    }

    public function updated(): void
    {
        error_log('updated');
        $queryse = $this->pass_queryfilters();
        $this->dispatch('updateProvisionSumary2');
        
    }

    public function resetTableFiltersForm(): void
    {
        error_log('resetTableFiltersForm');
        $queryse = $this->pass_queryfilters();
        $this->dispatch('updateProvisionSumary2');
    }

    public function removeTableFilters(): void
    {
        error_log('removeTableFilters');
        $queryse = $this->pass_queryfilters();
        $this->dispatch('updateProvisionSumary2');
    }

    public function isTableLoaded(): bool
    {
        error_log('isTableLoaded');
        $queryse = $this->pass_queryfilters();
        $this->dispatch('updateProvisionSumary2');

        return True;
    }

    public function pass_queryfilters(){

        $country_code = $this->table->getLivewire()->tableFilters['country_code']['value'];
        $curve_segment = $this->table->getLivewire()->tableFilters['curve_segment']['value'];
        $product = $this->table->getLivewire()->tableFilters['product']['value'];

        $queryse = '1<2';
        $queryse = $country_code ? "{$queryse} and country_code in ('{$country_code}')" : $queryse;
        $queryse = $curve_segment ? "{$queryse} and curve_segment in ('{$curve_segment}')" : $queryse;
        $queryse = $product ? "{$queryse} and product in ('{$product}')" : $queryse;

        session()->put('queryse', $queryse);

        return $queryse;
    }

    public function get_session_values(){

        $queryse = session()->get('queryse');

        $queryse = $queryse ? $queryse : '1<2';

        return $queryse;
    }

    protected function getTableHeading(): string
    {
        return __('Translatable Custom Heading');
    }
    
}
