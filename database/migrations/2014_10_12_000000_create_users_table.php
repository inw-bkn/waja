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

        // $u = new \App\Models\User();
        // $u->login = 'ko@ko.ko';
        // $u->name = 'koko';
        // $u->slug = \Str::uuid()->toString();
        // $u->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
