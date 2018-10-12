<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('social_id')->default(0)->comment('社交（朋友圈）ID');
            $table->integer('fan_id')->default(0)->comment('粉丝ID');
            $table->integer('pid')->default(0)->comment('楼层ID');
            $table->string('content')->default('')->comment('内容');  
            $table->tinyInteger('flag')->default(0)->comment('是否是回复内容，0:否，1:是');  
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
        Schema::dropIfExists('social_comments');
    }
}
