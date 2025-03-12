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
            'labels' => [-60,-30,0,6,9,11,16,17,27,30,33,47,60,90,252,630]
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
            $probs = [];

            for ($i = 0; $i <= 15; $i++) {
                $probs[] = $curve[$i];
            }

            $chart_data[] = ['label' => $segment, 'data' => $probs, 'borderColor' => '#9BD0F5'];
        }

        return $chart_data;
    }
}


/** 
//use App\Models\Provcurve;

$curves=Provcurve::query()
            ->where('country_code','CO')
            ->where('curve_segment','TOTAL')
            ->get()
            ->toArray();
$chart_data = [];

foreach($curves as $curve){

    $segment = $curve['product'];
    $probs = [];

    for ($i = 0; $i <= 15; $i++) {
        $probs[] = $curve[$i];
    }

    $chart_data[] = ['label' => $segment, 'data' => $probs, 'borderColor' => '#9BD0F5'];
}
    */