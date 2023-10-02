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
        Schema::create('facilities', function (Blueprint $table) {
            // Length declaration 
            $short = Template::getDataLength("short_char");
            $med = Template::getDataLength("med_char");
            $large = Template::getDataLength("large_char");
            
            $table->uuid('id')->primary();
            $table->string('name', $med);
            $table->string('type', $med);
            $table->string('location', $med);
            $table->string('country', $short);
            $table->string('coordinate', $large);

            // Properties
            $table->dateTime('created_at', $precision = 0);
            $table->string('created_by', 36);
            $table->dateTime('updated_at', $precision = 0)->nullable();
            $table->string('updated_by', 36)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facilities');
    }
};
