<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_rewards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fan_id')->default(0)->comment('粉丝ID');
            $table->integer('social_id')->default(0)->comment('动态ID');
            $table->integer('point')->default(0)->comment('奖励积分');            
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
        Schema::dropIfExists('social_rewards');
    }
}
