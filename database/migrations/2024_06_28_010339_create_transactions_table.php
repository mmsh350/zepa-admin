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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('payerid')->nullable();
            $table->string('referenceId')->nullable();
            $table->string('service_type')->nullable();
            $table->string('service_description')->nullable();
            $table->string('amount');
            $table->string('type')->nullable();
            $table->string('gateway')->nullable();
            $table->enum('status', ['Approved', 'Pending', 'Rejected'])->default('Pending');
            $table->string('payer_name')->nullable();
            $table->string('payer_phone')->nullable();
            $table->string('payer_email')->nullable();
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
        Schema::dropIfExists('transactions');
    }
};
