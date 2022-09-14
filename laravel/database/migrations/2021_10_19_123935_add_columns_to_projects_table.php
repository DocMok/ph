<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->addColumn('text', 'expected_revenue_and_profits')->after('logo')->nullable();
            $table->addColumn('text', 'total_time_frame_and_cost')->after('logo')->nullable();
            $table->addColumn('text', 'resources_available_needed')->after('logo')->nullable();
            $table->addColumn('text', 'product_or_service_description')->after('logo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('product_or_service_description');
            $table->dropColumn('resources_available_needed');
            $table->dropColumn('total_time_frame_and_cost');
            $table->dropColumn('expected_revenue_and_profits');
        });
    }
}
