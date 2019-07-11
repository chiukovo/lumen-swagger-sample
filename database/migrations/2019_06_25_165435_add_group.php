<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('domain')->default(0)->comment('登入站別');
            $table->string('name', 100)->comment('名稱');
            $table->text('permission')->nullable()->comment('權限');
            $table->enum('status', [0, 1])->default(1)->comment('0: 鎖定, 1: 開啟');
            $table->timestamps();
        });

        // Insert default
        app('db')->table('group')->insert([
            'name' => '一般階層',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group');
    }
}
