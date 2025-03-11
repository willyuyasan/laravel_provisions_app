<?php

namespace App\Filament\Resources\ProvinvoiceResource\Pages;

use App\Filament\Resources\ProvinvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProvinvoices extends ListRecords
{
    protected static string $resource = ProvinvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
