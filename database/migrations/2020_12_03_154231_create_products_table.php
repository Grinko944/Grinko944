<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->timestamps();
            $table->string("photo");
            $table->string("name");
            $table->string("name_RU");
            $table->string("name_BG");
            $table->string("name_DE");
            $table->text("description");
            $table->text("description_RU");
            $table->text("description_BG");
            $table->text("description_DE");
            $table->decimal("price");
            $table->decimal("price_EURO");
            $table->decimal("price_USD");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
