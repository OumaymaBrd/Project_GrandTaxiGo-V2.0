<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAdminsTableForVoyager extends Migration
{
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'avatar')) {
                $table->string('avatar')->nullable();
            }
            if (!Schema::hasColumn('admins', 'role_id')) {
                $table->bigInteger('role_id')->nullable();
            }
            if (!Schema::hasColumn('admins', 'settings')) {
                $table->text('settings')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'role_id', 'settings']);
        });
    }
}
