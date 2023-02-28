<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('stock_base_quantity')->comment('The quantity is in grams');; // Quantity is in grams
            $table->integer('stock_utilized_quantity')->comment('The q is in grams');; // Quantity is in grams
            $table->boolean('low_stock_warning_sent')->comment('The amount is in grams');; // On low stock, trigger warning and set low_stock_warning_sent as true
            $table->unique(['name']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
