<?php

namespace App\Filament\Resources\ProvinvoiceResource\Pages;

use Filament\Actions;
use App\Models\Provinvoice;
use Illuminate\Support\Str;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ProvinvoiceResource;
use Filament\Pages\Concerns\ExposesTableToWidgets;

class ListProvinvoices extends ListRecords
{
    protected static string $resource = ProvinvoiceResource::class;
    use ExposesTableToWidgets;
    

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // For showing the widget into the resource
    protected function getHeaderWidgets(): array
    {
        
        $country_code = $this->table->getLivewire()->tableFilters['country_code']['value'];

        //dd($country_code);
        $this->dispatch('updateProvisionSumary');

        if (empty($country_code)){
            $country_code = "CO','CL";
        }

        return [
            ProvinvoiceResource\Widgets\ProvisionSummary::make([
                'country_code' => $country_code
            ]),
        ];
    }

    public function updated($tableFilters)
    {
        if ($tableFilters) {
        $this->dispatch('updateProvisionSumary');
        }
    }
}

/*

$data = Provinvoice::get()->toArray();
        $country_code = $data['country_code'];
        $country_code = array_unique($country_code);
        $country_code = var_dump(implode(",", $country_code)); 


use App\Models\Provinvoice;
$data = Provinvoice::get()->toArray();

public function updateTableFilters(string $filter): void 
    { 
        $this->tableFilters[$filter]['isActive'] = true; 
    } 
*/
