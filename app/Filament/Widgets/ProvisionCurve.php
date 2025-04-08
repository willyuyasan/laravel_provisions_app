<?php

namespace App\Filament\Widgets;

use App\Models\Provcurve;
use App\Models\ProcessInfo;
use Filament\Widgets\ChartWidget;

class ProvisionCurve extends ChartWidget
{
    protected static ?string $heading = 'Provision Curves (KLYM)';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $chart_data = $this->getCurveProbs();
        $x_labels = $this->getCurvexLabels();

        return [
            //
            'datasets' => $chart_data,
            'labels' => $x_labels
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
            $lencurve = count($curve) - 12;

            for ($i = 0; $i <= $lencurve; $i++) {
                $probs[] = $curve[$i];
            }

            $chart_data[] = ['label' => $segment, 'data' => $probs, 'borderColor' => '#9BD0F5'];
        }

        return $chart_data;
    }

    private function getCurvexLabels():array {

        $curve_x_labels = ProcessInfo::query()->where('description','curve_x_labels')->max('value');
        $curve_x_labels = str_replace("{",'',$curve_x_labels);
        $curve_x_labels = str_replace("}",'',$curve_x_labels);
        $pieces = explode(",", $curve_x_labels);
        $len_pieces = count($pieces);

        $arr = [];
        for ($i = 0; $i < $len_pieces; $i++) {
            $arr[] = intval($pieces[$i]);
        }

        return $arr;
    }
}


/*
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