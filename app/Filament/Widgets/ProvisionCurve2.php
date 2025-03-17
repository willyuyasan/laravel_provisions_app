<?php

namespace App\Filament\Widgets;

use App\Models\Provcurve;
use App\Models\ProcessInfo;
use Filament\Widgets\ChartWidget;

class ProvisionCurve2 extends ChartWidget
{
    protected static ?string $heading = 'Provision Curves (COVAL)';
    protected static ?int $sort = 2;

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
*/
