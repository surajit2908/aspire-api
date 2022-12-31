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
        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('loan_id')->unsigned()->index()->nullable();
            $table->foreign('loan_id')->references('id')->on('loans')->onDelete('cascade');
            $table->double('amount', 8, 3)->default(0.00);
            $table->date('repayment_date');
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
        Schema::dropIfExists('loan_repayments', function (Blueprint $table) {
            $table->dropForeign('loan_repayments_loan_id_foreign');
            $table->dropIndex('loan_repayments_loan_id_index');
            $table->dropColumn('loan_id');
        });
    }
};
