<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTodaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('todays', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100)->default('')->comment('标题');            
            $table->string('thumb', 256)->default('')->comment('封面图片');  
            $table->string('text')->default('')->nullable()->comment('文字');  
            $table->string('music')->default('')->nullable()->comment('音乐');  
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
        Schema::dropIfExists('todays');
    }
}
