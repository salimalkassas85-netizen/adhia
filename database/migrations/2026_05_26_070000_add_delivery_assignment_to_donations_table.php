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
            if (! Schema::hasColumn('donations', 'assigned_agent_id')) {
                $table->foreignId('assigned_agent_id')->nullable()->after('assigned_admin_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('donations', 'assigned_at')) {
                $table->timestamp('assigned_at')->nullable()->after('assigned_agent_id');
            }

            if (! Schema::hasColumn('donations', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('assigned_at');
            }

            if (! Schema::hasColumn('donations', 'agent_notes')) {
                $table->text('agent_notes')->nullable()->after('admin_notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            if (Schema::hasColumn('donations', 'assigned_agent_id')) {
                $table->dropConstrainedForeignId('assigned_agent_id');
            }

            if (Schema::hasColumn('donations', 'assigned_at')) {
                $table->dropColumn('assigned_at');
            }

            if (Schema::hasColumn('donations', 'admin_notes')) {
                $table->dropColumn('admin_notes');
            }

            if (Schema::hasColumn('donations', 'agent_notes')) {
                $table->dropColumn('agent_notes');
            }
        });
    }
};
