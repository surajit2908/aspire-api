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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->double('amount', 8, 3)->default(0.00);
            $table->integer('term');
            $table->enum('status', ['PENDING', 'APPROVED'])->default('PENDING');
            $table->enum('payment_status', ['NOT_PAID', 'PAID'])->default('NOT_PAID');
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
        Schema::dropIfExists('loans', function (Blueprint $table) {
            $table->dropForeign('loans_user_id_foreign');
            $table->dropIndex('loans_user_id_index');
            $table->dropColumn('user_id');
        });
    }
};
