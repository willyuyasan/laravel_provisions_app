<?php

namespace App\Filament\Widgets;

use App\Models\ProcessInfo;
use App\Models\Provinvoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $prov_info = $this->provision_info();
        $end_curve_days = $prov_info['end_curve_days'];
        $pp_prov = $this->ppprov();
        
        return [
            //
            Stat::make('Fecha de Balance:', $prov_info['balance_date'])
                ->description("Maximo numero de dias estimado: {$end_curve_days} .\n 
                    Presione aqui para ver los archivos!")
                ->descriptionIcon('heroicon-m-link')
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ])
                ->url($prov_info['url_provisions_storage']),

            Stat::make('Total Facturas', $prov_info['invoices'])
                ->description('Facturas con saldo pendiente a la fecha')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Provision', $prov_info['provision'])
                ->description("{$pp_prov} % of provision")
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }

    public function provision_info()
    {
        $balance_date = ProcessInfo::query()->where('description','balance_date')->max('value');
        $end_curve_days = ProcessInfo::query()->where('description','end_curve_days')->max('value');
        $url_provisions_storage = ProcessInfo::query()->where('description','url_provisions_storage')->max('value');
        $invoices = number_format(Provinvoice::query()->count(), 0);
        $provision = number_format(Provinvoice::query()->sum('provision'), 0);

        return [
            'balance_date' => $balance_date,
            'end_curve_days' => $end_curve_days,
            'url_provisions_storage' => $url_provisions_storage,
            'invoices' => $invoices,
            'provision' => $provision
        ];
    }

    private function ppprov()
    {
        $prov = Provinvoice::query()->sum('provision');
        $debt = Provinvoice::query()->sum('actual_debt');
        $pp_prov = number_format(($prov/$debt)*100,2);
        return $pp_prov;
    }

    protected function getColumns(): int {
        return 3;
    }
}



//use App\Models\ProcessInfo;
//$url = ProcessInfo::query()->where('description','url_provisions_storage')->max('value')


//use App\Models\Provinvoice;
//$prov = Provinvoice::query()->sum('provision')
//$debt = Provinvoice::query()->sum('actual_debt')