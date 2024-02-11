<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('type')->comment('1: Ürüne Yüzde indirim kampanyası, 2: Sipariş toplamına yüzde indirim kampanyası, 3: X al y öde kampanyası'); 
            $table->double('price_min_limit');
            $table->integer('quantity_min_limit'); 
            $table->double('discount_percent');
            $table->integer('quantity_free');
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
        Schema::dropIfExists('campaigns');
    }
}
