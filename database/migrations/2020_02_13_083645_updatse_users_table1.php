<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatseUsersTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 新增微信小程序授权字段
        Schema::table('users', function (Blueprint $table) {
            $table->string('openid',100)->nullable($value = true)->comment('微信openID');
            $table->string('session_key',100)->nullable($value = true)->comment('微信授权会话密钥');
            $table->string('imageurl')->nullable($value = true)->comment('微信用户头像地址');
            $table->smallInteger('status')->nullable($value = true)->comment('用户状态[-1未授权，1已授权未绑定，2已授权已绑定]');
            $table->integer('relation_id')->nullable($value = true)->comment('关联用户ID');
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
