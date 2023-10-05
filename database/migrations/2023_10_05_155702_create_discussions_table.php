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
        Schema::create('discussions', function (Blueprint $table) {
            // Length declaration 
            $short = Template::getDataLength("short_char");
            
            $table->uuid('id')->primary();
            $table->string('stories_id', $short);
            $table->string('reply_id', $short)->nullable();
            $table->string('body', 2000);
            $table->longText('attachment')->nullable();

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
        Schema::dropIfExists('discussions');
    }
};
