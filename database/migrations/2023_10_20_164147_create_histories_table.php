<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            // Length declaration 
            $short = Template::getDataLength("short_char");
            $exshort = Template::getDataLength("exshort_char");
            $large = Template::getDataLength("large_char");
            
            $table->uuid('id')->primary();
            $table->string('history_type', $exshort);
            $table->string('body', $large);

            // Properties
            $table->dateTime('created_at', $precision = 0);
            $table->string('created_by', $short);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('histories');
    }
};
