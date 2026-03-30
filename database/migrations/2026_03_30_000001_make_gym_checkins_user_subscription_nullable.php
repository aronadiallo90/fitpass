<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rendre user_id et subscription_id nullables dans gym_checkins.
 * Nécessaire pour enregistrer les tentatives de scan invalides (QR inconnu, etc.)
 * sans forcer un user ou un abonnement.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gym_checkins', function (Blueprint $table) {
            // Supprimer les FK avant de modifier les colonnes
            $table->dropForeign(['user_id']);
            $table->dropForeign(['subscription_id']);

            $table->foreignUuid('user_id')->nullable()->change();
            $table->foreignUuid('subscription_id')->nullable()->change();

            // Recréer les FK avec nullable
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('gym_checkins', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['subscription_id']);

            $table->foreignUuid('user_id')->nullable(false)->change();
            $table->foreignUuid('subscription_id')->nullable(false)->change();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('restrict');
        });
    }
};
