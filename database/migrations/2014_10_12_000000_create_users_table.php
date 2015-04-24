<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
			$table->increments('id');
			$table->string('username', 128)->unique();
			$table->string('email', 240)->unique();
			$table->string('password', 60);
			$table->boolean('activated')->default(1);
			$table->boolean('banned')->default(0);
			$table->string('ban_reason', 240)->nullable();
			$table->string('new_password_key', 50)->nullable();
			$table->dateTime('new_password_requested')->nullable();
			$table->string('new_email', 240)->nullable();
			$table->string('new_email_key', 50)->nullable();
			$table->string('last_ip', 100);
			$table->dateTime('last_login');
			$table->rememberToken();
			$table->timestamps();
		});

		Schema::create('userprofiles', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->string('fullname', 240)->nullable();
			$table->text('last_browser')->nullable();
			$table->smallInteger('credential')->default(1);  /* default=1 means user online, can be 'admin'(2), 'cs'(3), or other */
			$table->smallInteger('group_id')->references('id')->on('groups')->default(1); /* default=1 means group online, please insert 1 for online in table groups */
			$table->string('phone', 250)->nullable();
			$table->text('about')->nullable();
			$table->date('day_of_birth')->nullable();
			$table->boolean('gender')->default(0);
			$table->string('postcode', 10)->nullable();
			$table->smallInteger('country_id')->default(107); /* default=107 means indonesia */
			$table->smallInteger('state_id')->nullable();
			$table->smallInteger('city_id')->nullable();
			$table->string('other_city')->nullable();
			$table->string('photo', 250)->nullable();
			$table->string('jobs', 250)->nullable();
			$table->string('position', 250)->nullable();
			$table->string('twitter', 100)->nullable();
			$table->string('facebook', 100)->nullable();
			$table->string('googleplus', 100)->nullable();
			$table->string('skype', 100)->nullable();
			$table->timestamps();
		});

		Schema::create('login_attempts', function(Blueprint $table) {
			$table->increments('id');
			$table->string('ip_address', 240);
			$table->string('login', 100);
			$table->timestamp('time')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
			$table->timestamps();
		});


		Schema::create('password_reminders', function(Blueprint $table)  /* build from laravel, i can not delete this */
		{
			$table->string('email')->index();
			$table->string('token')->index();
			$table->timestamp('created_at');
		});

		Schema::create('groups', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name', 240);
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
		Schema::drop('userprofiles');
		Schema::drop('login_attempts');
		Schema::drop('password_reminders');
		Schema::drop('groups');
	}

}
