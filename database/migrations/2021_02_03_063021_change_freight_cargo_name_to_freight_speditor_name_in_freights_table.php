<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFreightCargoNameToFreightSpeditorNameInFreightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('freights', function (Blueprint $table) {
            $table->renameColumn('freight_cargo_name', 'freight_speditor_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('freights', function (Blueprint $table) {
            $table->renameColumn('freight_speditor_name', 'freight_cargo_name');
        });
    }
}
