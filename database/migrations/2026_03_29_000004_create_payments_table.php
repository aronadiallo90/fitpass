<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('subscription_id')->constrained()->onDelete('restrict');
            $table->foreignUuid('user_id')->constrained()->onDelete('restrict');
            $table->string('paytech_ref')->unique()->nullable();   // clé d'idempotence webhook
            $table->string('paytech_token')->nullable();
            $table->enum('method', ['wave', 'orange_money']);
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->integer('amount_fcfa');
            $table->json('paytech_payload')->nullable(); // payload webhook (données sensibles masquées)
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('paytech_ref');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
