<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrateLeaderboardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaderboards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('img_id')->comment('图片id');
            $table->date('date')->comment('日期');
            $table->integer('ranking')->comment('名次');
            $table->integer('old_ranking')->comment('旧名次');
            $table->integer('up')->comment('上升');
            $table->tinyInteger('is_first')->comment('是否首次登场');
            $table->tinyInteger('is_hidden')->comment('是否隐藏，0是显示，1是隐藏');
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
        //
    }
}
