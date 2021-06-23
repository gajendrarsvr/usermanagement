<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSocialLoginColomnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('last_name');
            $table->string('google_id')->after('password')->nullable();
            $table->string('facebook_id')->after('google_id')->nullable();
            $table->string('apple_id')->after('facebook_id')->nullable();
            $table->string('password')->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('google_id');
            $table->dropColumn('facebook_id');
            $table->dropColumn('apple_id');
            $table->string('password')->change();
        });
    }
}
