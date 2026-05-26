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
        Schema::table('donations', function (Blueprint $table) {
            $table->foreignId('pickup_agent_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('pickup_assigned_at')->nullable()->after('pickup_agent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pickup_agent_id');
            $table->dropColumn('pickup_assigned_at');
        });
    }
};
