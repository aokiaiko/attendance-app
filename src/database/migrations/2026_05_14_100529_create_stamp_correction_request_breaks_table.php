<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStampCorrectionRequestBreaksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stamp_correction_request_breaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stamp_correction_request_id');
            $table->foreign('stamp_correction_request_id', 'scr_breaks_request_fk')
                 ->references('id')
                 ->on('stamp_correction_requests')
                 ->cascadeOnDelete();
            $table->dateTime('requested_break_start')->nullable();
            $table->dateTime('requested_break_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stamp_correction_request_breaks');
    }
}
