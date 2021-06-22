<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'user_id_fk_4188017')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('app_id');
            $table->foreign('app_id', 'app_id_fk_4188017')->references('id')->on('apps')->onDelete('cascade');
            $table->unsignedBigInteger('subscription_plan');
            $table->foreign('subscription_plan', 'subscription_plan_fk_4188017')->references('id')->on('subscription_plans')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
