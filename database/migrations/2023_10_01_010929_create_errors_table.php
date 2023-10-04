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
        Schema::create('errors', function (Blueprint $table) {
            // Length declaration 
            $short = Template::getDataLength("short_char");
            $exshort = Template::getDataLength("exshort_char");
            $mini = Template::getDataLength("mini_char");

            $table->bigInteger('id')->length($exshort)->primary();
            $table->text('message');
            $table->text('stack_trace');
            $table->string('file', $exshort);
            $table->integer('line')->length($mini)->unsigned();
            $table->string('faced_by', $short)->nullable();

            $table->timestamp('created_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('errors');
    }
};
