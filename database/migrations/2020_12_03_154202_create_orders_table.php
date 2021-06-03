<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->integer("date")->unsigned();
            $table->timestamps();
            $table->string("product_ids");
            $table->bigInteger("user_id");
            $table->decimal("price");
            $table->string("status");
            $table->string("name_RU");
            $table->string("delivery_time");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
