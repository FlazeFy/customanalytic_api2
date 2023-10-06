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
        Schema::create('stories', function (Blueprint $table) {
            // Length declaration 
            $exshort = Template::getDataLength("exshort_char");
            $short = Template::getDataLength("short_char");
            $med = Template::getDataLength("med_char");
            $exmed = Template::getDataLength("exmed_char");

            $table->uuid('id')->primary();
            $table->string('main_title', $med);
            $table->boolean('is_finished');
            $table->string('story_type', $exshort);
            $table->date('date_start', $precision = 0);
            $table->date('date_end',  $precision = 0)->nullable();
            $table->string('story_result', $exshort)->nullable();
            $table->string('story_location', $exmed);
            $table->longText('story_tag');
            $table->longText('story_detail');
            $table->longText('story_stats')->nullable();
            $table->longText('story_reference');

            // Properties
            $table->dateTime('created_at', $precision = 0);
            $table->string('created_by', $short);
            $table->dateTime('updated_at', $precision = 0)->nullable();
            $table->string('updated_by', $short)->nullable();
            $table->dateTime('deleted_at', $precision = 0)->nullable();
            $table->string('deleted_by', $short)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stories');
    }
};
