<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Provcurve;

class ProvisionCurve extends ChartWidget
{
    protected static ?string $heading = 'Provision Curves (KLYM)';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $chart_data = $this->getCurveProbs();

        return [
            //
            'datasets' => $chart_data,
            'labels' => [-70, -30, 0, 30, 60, 90]
            ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getCurveProbs():array {

        $curves=Provcurve::query()
            ->where('country_code','CO')
            ->where('curve_segment','TOTAL')
            ->get()
            ->toArray();

        $chart_data = [];

        foreach($curves as $curve){

            $segment = $curve['product'];
            $probs = [$curve[74-73], $curve[74-73+29],$curve[74], $curve[74+29], $curve[74+59], $curve[74+89]];

            $chart_data[] = ['label' => $segment, 'data' => $probs, 'borderColor' => '#9BD0F5'];
        }

        return $chart_data;
    }
}





$curve=Provcurve::query()
            ->get()
            ->toArray();