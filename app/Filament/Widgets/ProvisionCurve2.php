<?php

namespace App\Filament\Widgets;

use App\Models\Provcurve;
use Filament\Widgets\ChartWidget;

class ProvisionCurve2 extends ChartWidget
{
    protected static ?string $heading = 'Provision Curves (COVAL)';
    protected static ?int $sort = 2;

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
            ->where('country_code','CL')
            ->where('curve_segment','COVAL')
            ->get()
            ->toArray();

        $chart_data = [];

        foreach($curves as $curve){

            $segment = $curve['product'];
            $probs = [$curve[74-73], $curve[74-73+29],$curve[74], $curve[74+29], $curve[74+59], $curve[74+89]];

            $chart_data[] = ['label' => $segment, 'data' => $probs];
        }

        return $chart_data;
    }
}
