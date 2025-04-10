<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Provinvoice;
use App\Models\ProvTranches;
use Filament\Widgets\TableWidget;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Facades\FilamentColor;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Database\Eloquent\Builder;


FilamentColor::register([
    'danger2' => Color::hex('#b0347f'), //purple
]);

class ProvisionSummary2 extends TableWidget
{
    protected static ?string $heading = 'Total Provision (Altura Mora)';
    protected static ?int $sort = 5;

    protected $listeners = ['updateProvisionSumary' => '$refresh'];
    protected static ?string $pollingInterval = null;
    public string $queryse;

    public function table(Table $table): Table
    {
        $queryse = $this->get_session_values();
        error_log($queryse);

        return $table
            ->query(
                ProvTranches::query()
                ->select(DB::raw('
                    age_range
                    ,min(id) as id
                    ,min(tranch_priority) as tranch_priority
                    ,sum(invoices) as invoices
                    ,sum(actual_debt) as actual_debt
                    ,sum(provision) as provision
                    '
                    ))
                ->groupBy('age_range')
                ->orderBy('tranch_priority','asc') //mandatory for allow laravel to execute the query
                ->whereRaw("{$queryse}")
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
                    }),

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
                ]);
    }

    public function get_session_values(){

        $queryse = session()->get('queryse');

        $queryse = $queryse ? $queryse : '1<2';

        return $queryse;
    }
}

/*
Provinvoice::query()
    ->select(DB::raw('
        age_range
        ,min(id) as id
        ,count(*) as invoices
        ,sum(actual_debt) as actual_debt
        ,sum(provision) as provision
        '
        ))
    ->groupBy('age_range')
    ->orderBy('id','desc') //mandatory for allow laravel to execute the query
*/