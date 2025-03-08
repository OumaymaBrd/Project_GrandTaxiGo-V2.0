<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNeedsRatingToRideRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('ride_requests', function (Blueprint $table) {
            $table->boolean('needs_rating')->default(false);
        });
    }

    public function down()
    {
        Schema::table('ride_requests', function (Blueprint $table) {
            $table->dropColumn('needs_rating');
        });
    }
}
