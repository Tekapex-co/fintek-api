<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained();
            $table->string('account_name');
            $table->string('account_number');
            $table->decimal('balance', 10, 2)
                ->default(0.00);
            $table->decimal('book_balance', 10, 2)
                ->default(0.00);
            $table->string('currency')->default('NGN');
            $table->string('status');
            $table->string('type');
            $table->decimal('interest_rate');
            $table->string('interest_type');
            $table->string('interest_period');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
