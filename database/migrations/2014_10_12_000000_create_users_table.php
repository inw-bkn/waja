<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('slug');
            $table->string('name')->unique();
            $table->string('email')->nullable()->index();
            $table->string('password')->nullable();
            $table->json('profile');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('abilities', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        Schema::create('ability_role', function (Blueprint $table) {
            $table->primary(['role_id', 'ability_id']);
            $table->unsignedSmallInteger('role_id')->index();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->unsignedSmallInteger('ability_id')->index();
            $table->foreign('ability_id')->references('id')->on('abilities')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->primary(['user_id', 'role_id']);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedSmallInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->timestamps();
        });

        $root = new \App\Models\Role();
        $root->name = 'root';
        $root->save();

        $dev = new \App\Models\Role();
        $dev->name = 'developer';
        $dev->save();

        $ability = new \App\Models\Ability();
        $ability->name = 'grant_developer'; 
        $ability->save();
        $root->allowTo($ability);

        $ability = new \App\Models\Ability();
        $ability->name = 'revoke_developer'; 
        $ability->save();
        $root->allowTo($ability);

        $ability = new \App\Models\Ability();
        $ability->name = 'get_user'; 
        $ability->save();
        $dev->allowTo($ability);

        $ability = new \App\Models\Ability();
        $ability->name = 'add_bot'; 
        $ability->save();
        $dev->allowTo($ability);

        $ability = new \App\Models\Ability();
        $ability->name = 'remove_bot'; 
        $ability->save();
        $dev->allowTo($ability);

        $ability = new \App\Models\Ability();
        $ability->name = 'offer_bot_to_user'; 
        $ability->save();
        $dev->allowTo($ability);

        $ability = new \App\Models\Ability();
        $ability->name = 'get_bot_update'; 
        $ability->save();
        $dev->allowTo($ability);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('ability_role');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('abilities');
        Schema::dropIfExists('users');
    }
}
