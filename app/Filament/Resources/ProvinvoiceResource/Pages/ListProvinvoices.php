<?php

namespace App\Filament\Resources\ProvinvoiceResource\Pages;

use Filament\Actions;
use App\Models\Provinvoice;
use Illuminate\Support\Str;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ProvinvoiceResource;
use App\Filament\Resources\ProvinvoiceResource\Widgets\ProvisionSummary;
use Filament\Pages\Concerns\ExposesTableToWidgets;

class ListProvinvoices extends ListRecords
{
    protected static string $resource = ProvinvoiceResource::class;
    use ExposesTableToWidgets;

    public $queryse;
    

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // For showing the widget into the resource
    protected function getHeaderWidgets(): array
    {
        $queryse = $this->pass_queryfilters();
        $this->dispatch('updateProvisionSumary');

        return [
            ProvisionSummary::class
        ];
    }

    public function updated($name)
    {
        if (Str::of($name)->contains(['mountedTableAction', 'mountedTableBulkAction','tableFilters'])) {

            $queryse = $this->pass_queryfilters();
            $this->dispatch('updateProvisionSumary');
        }
    }

    public function pass_queryfilters(){

        $country_code = $this->table->getLivewire()->tableFilters['country_code']['value'];
        $product = $this->table->getLivewire()->tableFilters['product']['value'];
        $curve_segment = $this->table->getLivewire()->tableFilters['curve_segment']['value'];

        $queryse = 'where 1<2';
        $queryse = $country_code ? "{$queryse} and country_code in ('{$country_code}')" : $queryse;
        $queryse = $product ? "{$queryse} and product in ('{$product}')" : $queryse;
        $queryse = $curve_segment ? "{$queryse} and curve_segment in ('{$curve_segment}')" : $queryse;

        //error_log($queryse);

        session()->put('queryse', $queryse);

        return $queryse;
    }
}

/*

return [
            ProvinvoiceResource\Widgets\ProvisionSummary::make([
                'country_code' => $country_code
            ]),
        ];

        if (empty($country_code)){
            $country_code = "CO','CL";
        }
        else{
            $country_code = $country_code;
        }
*/
