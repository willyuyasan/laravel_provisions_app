<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        DB::statement("
        CREATE OR REPLACE VIEW prov_tranches AS
        with
        tranches as (
            SELECT
            country_code
            ,curve_segment
            ,product
            ,age_range
            ,count(*) as invoices
            ,sum(actual_debt) as actual_debt
            ,sum(provision) as provision
            from provinvoices
            group by
            country_code
            ,curve_segment
            ,product
            ,age_range
        )
        SELECT
        row_number() over() as id
        ,A.*

        ,case
            when A.age_range in ('VIGENTE') then 1
            when A.age_range in ('1-15') then 2
            when A.age_range in ('16-30') then 3
            when A.age_range in ('31-60') then 4
            when A.age_range in ('61-90') then 5
            when A.age_range in ('91-120') then 6
            when A.age_range in ('121-180') then 7
            when A.age_range in ('180+') then 8
        end as tranch_priority

        , provision/actual_debt as perc_provision
        from tranches A
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        DB::statement('DROP VIEW IF EXISTS prov_tranches;');
    }
};
