<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewUsersOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_orders', function (Blueprint $table) {
            $table->string('post_date_time')->nullable();
            $table->string('track_number')->nullable();
            $table->string('city')->nullable();
            $table->string('postal')->nullable();
             $table->string('download_link_compress')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_orders', function (Blueprint $table) {
            $table->dropColumn('post_date_time');
            $table->dropColumn('track_number');
            $table->dropColumn('city');
            $table->dropColumn('postal');
            $table->dropColumn('download_link_compress');
        });
    }
}
