<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('domain')->default(0)->comment('登入站別');
            $table->integer('pid')->default(0)->comment('上層id');
            $table->integer('group_id')->default(1)->comment('群組 預設1');
            $table->string('username', 30)->unique()->comment('帳號');
            $table->string('password')->comment('密碼');
            $table->string('name', 100)->comment('名稱');
            $table->string('email', 100)->comment('email');
            $table->enum('status', [0, 1])->default(1)->comment('0: 鎖定, 1: 開啟');
            $table->dateTime('last_login')->nullable()->comment('最後登入時間');
            $table->string('last_ip')->nullable()->comment('最後登入IP');
            $table->timestamps();
        });

        // Insert default
        app('db')->table('user')->insert([
            'username' => 'admin',
            'password' => app('hash')->make('password'),
            'name' => 'admin',
            'email' => '1@mail.com',
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
        Schema::dropIfExists('user');
    }
}
