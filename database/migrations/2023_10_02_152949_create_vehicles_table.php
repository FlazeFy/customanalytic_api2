<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Helpers\Template;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            // Length declaration 
            $short = Template::getDataLength("short_char");
            $med = Template::getDataLength("med_char");
            $exmed = Template::getDataLength("exmed_char");
            
            $table->uuid('id')->primary();
            $table->string('name', $med);
            $table->string('primary_role', $short);
            $table->string('manufacturer',  $exmed);
            $table->string('country', $short);

            // Properties
            $table->dateTime('created_at', $precision = 0);
            $table->string('created_by', $short);
            $table->dateTime('updated_at', $precision = 0)->nullable();
            $table->string('updated_by', $short)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
};
