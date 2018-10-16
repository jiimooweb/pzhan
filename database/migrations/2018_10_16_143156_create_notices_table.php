<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fan_id')->default(0)->comment('粉丝ID');
            $table->integer('modeul_id')->default(0)->comment('模块内容ID');
            $table->string('modeul', 30)->default('')->comment('模块');
            $table->tinyInteger('type')->default(0)->comment('通知类型，0:点赞，1:评论');
            $table->tinyInteger('status')->default(0)->comment('是否通知，0:否，1:是');
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
        Schema::dropIfExists('notices');
    }
}
