<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSwiperGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('swiper_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',50)->default('')->comment('轮播图名'); 
            $table->tinyInteger('display')->default(0)->comment('是否显示'); 
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
        Schema::dropIfExists('swiper_groups');
    }
}
