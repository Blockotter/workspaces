<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('website')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->boolean('is_always_open')->default(false);
            $table->decimal('cost', 5, 2)->default(0);
            $table->integer('minimum_hours')->default(0);
            $table->time('opening_monday')->nullable();
            $table->time('closing_monday')->nullable();
            $table->time('opening_tuesday')->nullable();
            $table->time('closing_tuesday')->nullable();
            $table->time('opening_wednesday')->nullable();
            $table->time('closing_wednesday')->nullable();
            $table->time('opening_thursday')->nullable();
            $table->time('closing_thursday')->nullable();
            $table->time('opening_friday')->nullable();
            $table->time('closing_friday')->nullable();
            $table->time('opening_saturday')->nullable();
            $table->time('closing_saturday')->nullable();
            $table->time('opening_sunday')->nullable();
            $table->time('closing_sunday')->nullable();
            $table->string('airtable_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('packages');
    }
}
