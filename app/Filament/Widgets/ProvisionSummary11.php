<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Provinvoice;
use App\Models\ProvTranches;
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

class ProvisionSummary11 extends BaseWidget
{
    protected static ?string $heading = 'Total Provision COVAL (Productos)';
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Provinvoice::query()
                ->select(DB::raw("
                    *
                    ,provision/actual_debt as perc_provision
                    "))
                ->from(DB::raw("
                        (
                        select
                        product
                        ,min(id) as id
                        ,count(*) as invoices
                        ,sum(actual_debt) as actual_debt
                        ,sum(provision) as provision
                        from provinvoices
                        where
                        curve_segment in ('COVAL')
                        group by
                        product
                        ) as T"
                        ))
                ->orderBy('provision','desc') //mandatory for allow laravel to execute the query
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
                    ->grow(false)
                    ->numeric(decimalPlaces: 3),
            ]);
    }
}
