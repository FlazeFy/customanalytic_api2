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
        Schema::create('feedbacks', function (Blueprint $table) {
            // Length declaration 
            $short = Template::getDataLength("short_char");
            $large = Template::getDataLength("large_char");
            
            $table->uuid('id')->primary();
            $table->string('stories_id', $short);
            $table->string('body', $large);
            $table->integer('rate')->length(2)->unsigned();

            // Properties
            $table->dateTime('created_at', $precision = 0);
            $table->string('created_by', $short)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feedbacks');
    }
};
