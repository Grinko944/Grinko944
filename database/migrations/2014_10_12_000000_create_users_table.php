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
            $table->bigIncrements("id");
            $table->string('name');
            $table->string('email')->unique();
            // $table->string('phone')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->default("");
            $table->text('password_encrypted')->default("");
            $table->integer("role_id")->unsigned()->default(4);
            $table->string("social_user_id")->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

//        Schema::table('users', function(Blueprint $table)
//        {
//            $table->foreign("role_id")
//                ->on("roles")
//                ->references("id")
//                ->onDelete("cascade");
//        });

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
