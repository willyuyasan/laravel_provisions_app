<?php

namespace App\Traits;

use App\Models\ProcessInfo;
use App\Models\Provinvoice;

trait MyUtils
 {

   public function provision_info()
   {
       $balance_date = ProcessInfo::query()->where('description','balance_date')->max('value');
       $end_curve_days = ProcessInfo::query()->where('description','end_curve_days')->max('value');

       $url_provisions_storage = ProcessInfo::query()->where('description','url_provisions_storage')->max('value');
       //$url_provisions_storage = str_replace("https:",'',$url_provisions_storage);
       //$url_provisions_storage = str_replace("http:",'',$url_provisions_storage);

       $invoices = Provinvoice::query()->count();
       $invoices_klym = Provinvoice::query()->where('curve_segment','TOTAL')->count();
       $invoices_coval = Provinvoice::query()->where('curve_segment', 'COVAL')->count();
       
       $pp_invs = number_format(100,0);
       $pp_invs_klym = number_format(($invoices_klym/$invoices)*100,2);
       $pp_invs_coval = number_format(($invoices_coval/$invoices)*100,2);

       $provision = Provinvoice::query()->sum('provision');
       $provision_klym = Provinvoice::query()->where('curve_segment','TOTAL')->sum('provision');
       $provision_coval = Provinvoice::query()->where('curve_segment', 'COVAL')->sum('provision');

       $pp_prov = number_format(100,0);
       $pp_prov_klym = number_format(($provision_klym/$provision)*100,2);
       $pp_prov_coval = number_format(($provision_coval/$provision)*100,2);

       $debt = Provinvoice::query()->sum('actual_debt');
       $debt_klym = Provinvoice::query()->where('curve_segment','TOTAL')->sum('actual_debt');
       $debt_coval = Provinvoice::query()->where('curve_segment', 'COVAL')->sum('actual_debt');

       $pp_ap = number_format(($provision/$debt)*100,2);
       $pp_ap_klym = number_format(($provision_klym/$debt_klym)*100,2);
       $pp_ap_coval = number_format(($provision_coval/$debt_coval)*100,2);

       $invoices = number_format($invoices, 0);
       $invoices_klym = number_format($invoices_klym, 0);
       $invoices_coval = number_format($invoices_coval, 0);

       $provision = number_format($provision, 0);
       $provision_klym = number_format($provision_klym, 0); 
       $provision_coval = number_format($provision_coval, 0);

       $debt = number_format($debt, 0);
       $debt_klym = number_format($debt_klym, 0);
       $debt_coval = number_format($debt_coval, 0);

       return [
           'balance_date' => $balance_date,
           'end_curve_days' => $end_curve_days,
           'url_provisions_storage' => $url_provisions_storage,

           'invoices' => $invoices,
           'invoices_klym' => $invoices_klym,
           'invoices_coval' => $invoices_coval,

           'pp_invs' => $pp_invs,
           'pp_invs_klym' => $pp_invs_klym,
           'pp_invs_coval' => $pp_invs_coval,

           'provision' => $provision,
           'provision_klym' => $provision_klym,
           'provision_coval' => $provision_coval,

           'pp_prov' => $pp_prov,
           'pp_prov_klym' => $pp_prov_klym,
           'pp_prov_coval' => $pp_prov_coval,

           'pp_ap' => $pp_ap,
           'pp_ap_klym' => $pp_ap_klym,
           'pp_ap_coval' => $pp_ap_coval,
       ];
   }

 }