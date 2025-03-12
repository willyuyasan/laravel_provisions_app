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
            'labels' => [-60,-30,0,6,9,11,16,17,27,30,33,47,60,90,252,630]
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
            $probs = [];

            for ($i = 0; $i <= 15; $i++) {
                $probs[] = $curve[$i];
            }

            $chart_data[] = ['label' => $segment, 'data' => $probs];
        }

        return $chart_data;
    }
}
