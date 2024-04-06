<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->date('activity_date')->nullable();
            $table->string('activity_rev')->nullable();
            $table->string('activity_dc')->nullable();
            $table->date('activity_check_in_l')->nullable();
            $table->date('activity_check_in_z')->nullable();
            $table->date('activity_check_out_l')->nullable();
            $table->date('activity_check_out_z')->nullable();
            $table->string('activity')->nullable();
            $table->string('activity_remark')->nullable();
            $table->string('activity_from')->nullable();
            $table->date('activity_from_std_l')->nullable();
            $table->date('activity_from_std_z')->nullable();
            $table->string('activity_to')->nullable();
            $table->date('activity_to_std_l')->nullable();
            $table->date('activity_to_std_z')->nullable();
            $table->string('activity_hotel')->nullable();
            $table->string('activity_blh')->nullable();
            $table->date('activity_flight_time')->nullable();
            $table->date('activity_night_time')->nullable();
            $table->string('activity_dur')->nullable();
            $table->string('activity_ext')->nullable();
            $table->string('activity_pax_booked')->nullable();
            $table->string('activity_acreg')->nullable();
            $table->string('activity_crew_meal')->nullable();
            $table->string('activity_resources')->nullable();
            $table->string('activity_cc')->nullable();
            $table->string('activity_name')->nullable();
            $table->string('activity_pos')->nullable();
            $table->string('activity_work_phone')->nullable();
            $table->string('activity_hd_crew')->nullable();
            $table->string('activity_hd_name')->nullable();
            $table->string('activity_hd_seat')->nullable();
            $table->string('activity_remarks')->nullable();
            $table->string('activity_fdp_time')->nullable();
            $table->string('activity_max_fdp')->nullable();
            $table->string('activity_rest_compl')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('activity_date');
            $table->dropColumn('activity_rev');
            $table->dropColumn('activity_dc');
            $table->dropColumn('activity_check_in_l');
            $table->dropColumn('activity_check_in_z');
            $table->dropColumn('activity_check_out_l');
            $table->dropColumn('activity_check_out_z');
            $table->dropColumn('activity');
            $table->dropColumn('activity_remark');
            $table->dropColumn('activity_from');
            $table->dropColumn('activity_from_std_l');
            $table->dropColumn('activity_from_std_z');
            $table->dropColumn('activity_to');
            $table->dropColumn('activity_to_std_l');
            $table->dropColumn('activity_to_std_z');
            $table->dropColumn('activity_hotel');
            $table->dropColumn('activity_blh');
            $table->dropColumn('activity_flight_time');
            $table->dropColumn('activity_night_time');
            $table->dropColumn('activity_dur');
            $table->dropColumn('activity_ext');
            $table->dropColumn('activity_pax_booked');
            $table->dropColumn('activity_acreg');
            $table->dropColumn('activity_crew_meal');
            $table->dropColumn('activity_resources');
            $table->dropColumn('activity_cc');
            $table->dropColumn('activity_name');
            $table->dropColumn('activity_pos');
            $table->dropColumn('activity_work_phone');
            $table->dropColumn('activity_hd_crew');
            $table->dropColumn('activity_hd_name');
            $table->dropColumn('activity_hd_seat');
            $table->dropColumn('activity_remarks');
            $table->dropColumn('activity_fdp_time');
            $table->dropColumn('activity_max_fdp');
            $table->dropColumn('activity_rest_compl');
        });
    }
};
