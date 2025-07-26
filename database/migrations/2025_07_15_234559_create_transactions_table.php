<?php

use App\Enums\FailureReason;
use App\Enums\Status;
use App\Enums\TransactionType;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('reference')->unique();
            $table->foreignUlid('from_account_id')->constrained('accounts');
            $table->foreignUlid('to_account_id')->constrained('accounts');
            $table->enum('type', TransactionType::values());
            $table->decimal('amount', 15, 2);
            $table->decimal('from_account_running_balance', 15, 2)->nullable();
            $table->decimal('to_account_running_balance', 15, 2)->nullable();
            $table->enum('status', Status::values());
            $table->string('note')->nullable();
            $table->foreignUlid('initiated_by')->constrained('users');
            $table->timestamp('processed_at')->nullable();
            $table->string('failure_reason', FailureReason::values())->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
