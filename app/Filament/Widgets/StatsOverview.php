<?php

namespace App\Filament\Widgets;

use App\Models\ProcessInfo;
use App\Models\Provinvoice;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $pp_prov = $this->ppprov();

        return [
            //
            Stat::make('Total Facturas', number_format(Provinvoice::query()->count(), 0))
                ->description('Presione aqui para ver los archivos!')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => 'goto()',
                ]),
            Stat::make('Total Provision', number_format(Provinvoice::query()->sum('provision'), 0))
                ->description("{$pp_prov} % of provision")
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }

    public function goto()
    {
        return redirect()->away(ProcessInfo::query()->where('description','url_provisions_storage')->max('value'));
    }

    private function ppprov()
    {
        $prov = Provinvoice::query()->sum('provision');
        $debt = Provinvoice::query()->sum('actual_debt');
        $pp_prov = number_format(($prov/$debt)*100,2);
        return $pp_prov;
    }

    protected function getColumns(): int {
        return 2;
    }
}



//use App\Models\ProcessInfo;
//$url = ProcessInfo::query()->where('description','url_provisions_storage')->max('value')


//use App\Models\Provinvoice;
//$prov = Provinvoice::query()->sum('provision')
//$debt = Provinvoice::query()->sum('actual_debt')