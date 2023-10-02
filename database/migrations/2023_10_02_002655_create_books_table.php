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
        Schema::create('books', function (Blueprint $table) {
            // Length declaration 
            $med = Template::getDataLength("med_char");
            $exmed = Template::getDataLength("exmed_char");
            
            $table->uuid('id')->primary();
            $table->string('title', $exmed);
            $table->string('author', $med);
            $table->string('reviewer', $med);
            $table->date('review_date', $precision = 0);

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
        Schema::dropIfExists('books');
    }
};
