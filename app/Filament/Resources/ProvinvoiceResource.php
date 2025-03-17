<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Provinvoice;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProvinvoiceResource\Pages;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use App\Filament\Resources\ProvinvoiceResource\RelationManagers;

class ProvinvoiceResource extends Resource
{
    protected static ?string $model = Provinvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('id_invoice')
                    ->size(TextColumnSize::ExtraSmall)
                    ->copyable()
                    ->wrap()
                    //->lineClamp(2)
                    ->grow(false)
                    ->searchable(),

                TextColumn::make('country_code')
                    ->grow(false),
                
                TextColumn::make('product')
                    ->grow(false),
                
                TextColumn::make('curve_segment')
                    ->grow(false),
                
                TextColumn::make('days_rel_due')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),
                
                TextColumn::make('actual_debt')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

                TextColumn::make('perc_provision')
                    ->grow(false)
                    ->numeric(decimalPlaces: 6),

                TextColumn::make('provision')
                    ->grow(false)
                    ->numeric(decimalPlaces: 2),
                
                TextColumn::make('provision_obs')
                    ->grow(false),
                
                TextColumn::make('issuer_name')
                    ->description(fn ($record): string => $record->issuer_tax_number)
                    ->limit(20)
                    ->grow(false)
                    ->searchable(['issuer_name','issuer_tax_number']),

                TextColumn::make('debtor_name')
                    ->description(fn ($record): string => $record->issuer_tax_number)
                    ->limit(20)
                    ->grow(false)
                    ->searchable(['debtor_name','debtor_tax_number']),

                TextColumn::make('funder_name')
                    ->description(fn ($record): string => $record->issuer_tax_number)
                    ->limit(20)
                    ->grow(false)
                    ->searchable(['funder_name','funder_tax_number']),

            ])
            ->defaultSort('provision','desc')

            ->filters([
                //
                SelectFilter::make('country_code')
                ->options(fn (): array => Provinvoice::query()->pluck('country_code','country_code')->all()),

                SelectFilter::make('product')
                ->options(fn (): array => Provinvoice::query()->pluck('product','product')->all()),

                SelectFilter::make('curve_segment')
                ->options(fn (): array => Provinvoice::query()->pluck('curve_segment','curve_segment')->all()),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProvinvoices::route('/'),
            'create' => Pages\CreateProvinvoice::route('/create'),
            'edit' => Pages\EditProvinvoice::route('/{record}/edit'),
        ];
    }

    // Registering the new widget
    public static function getWidgets(): array
    {
        return [
            ProvinvoiceResource\Widgets\ProvisionSummary::class,
        ];
    }
}
