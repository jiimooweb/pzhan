<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_notices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 20)->default('')->comment('标题');
            $table->string('content', 200)->default('')->comment('内容');
            $table->tinyInteger('hidden')->nullable()->default(0)->comment('是否显示');
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
        Schema::dropIfExists('social_notices');
    }
}
