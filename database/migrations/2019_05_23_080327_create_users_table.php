<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
            $table->bigInteger('id', true)->unsigned();
            $table->string('login')->unique('users_login_unique');
            $table->string('password');
            $table->string('role')->default('user');
            $table->string('note')->nullable();
            $table->string('phone')->nullable()->unique('users_phone_unique');
            $table->boolean('enabled')->default(1);
            $table->boolean('validated')->default(0);
            $table->dateTime('validated_at')->nullable();
            $table->string('validation_code')->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
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
		Schema::drop('users');
	}

}
