<?php

namespace App\Filament\Resources\ProvinvoiceResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Provinvoice;
use Illuminate\Support\Str;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Grouping\Group;
use Illuminate\Support\Facades\Log;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use App\Filament\Resources\ProvinvoiceResource\Pages\ListProvinvoices;

//packages/widgets/resources/views/chart-widget.blade.php

class ProvisionSummary extends TableWidget
{

    protected $listeners = ['updateProvisionSumary' => '$refresh'];

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = 'full';

    // you have to modify blade (/app/vendor/filament/widgets/resources/views/table-widget.blade.php)
    // you have to modify php (/app/vendor/filament/widgets/src/TableWidget.php)
    protected static bool $collapsible = true; 
    
    public string $queryse;


    public function table(Table $table): Table
    {
        $queryse = session()->get('queryse');
        error_log($queryse);

        return $table
            ->query(
                // ...
                Provinvoice::query()
                ->select(DB::raw("
                    *
                    , 'ALL' as all
                    ,provision/actual_debt as perc_provision

                    ,case
                        when T.age_range in ('VIGENTE') then 1
                        when T.age_range in ('1-15') then 2
                        when T.age_range in ('16-30') then 3
                        when T.age_range in ('31-60') then 4
                        when T.age_range in ('61-90') then 5
                        when T.age_range in ('91-120') then 6
                        when T.age_range in ('121-180') then 7
                        when T.age_range in ('180+') then 8
                    end as tranch_priority
                    "))
                ->from(DB::raw("
                        (
                        select
                        age_range
                        ,min(id) as id
                        ,count(*) as invoices
                        ,sum(actual_debt) as actual_debt
                        ,sum(provision) as provision
                        from provinvoices
                        {$queryse}
                        group by
                        age_range
                        ) as T"
                        ))
                ->orderBy('tranch_priority')
            )
            ->columns([
                // ...
                TextColumn::make('age_range')
                    ->grow(false)
                    ->badge()
                    ->color(fn (string $state): string=>match($state) {
                        'VIGENTE' => 'success',
                        '1-15' => 'warning',
                        '16-30' => 'danger2',
                        '31-60' => 'danger2',
                        '61-90' => 'danger2',
                        '91-120' => 'danger',
                        '121-180' => 'danger',
                        '180+' => 'danger',
                    })
                    ->visibleFrom('md'),

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
                    ->grow(false)
                    ->numeric(decimalPlaces: 3),

                
                ])
                ->filters([
                    //
                    SelectFilter::make('age_range')
                    ->options(fn (): array => Provinvoice::query()->pluck('age_range','age_range')->all()),
                ]);
    }

    public function updated($name)
    {
        if (Str::of($name)->contains(['tableFilters'])) {

            $queryse = $this->pass_queryfilters();
            $this->dispatch('updateProvisionSumary');
        }
    }

    public function pass_queryfilters(){

        $age_range = $this->table->getLivewire()->tableFilters['age_range']['value'];

        //error_log($queryse);

        session()->put('age_range', $age_range);

        return $age_range;
    }
}


/*


    
*/