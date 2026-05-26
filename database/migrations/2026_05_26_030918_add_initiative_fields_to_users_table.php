<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'agent'])->default('agent')->after('password');
            $table->foreignId('area_id')->nullable()->after('role')->constrained()->nullOnDelete();
            $table->timestamp('pledge_accepted_at')->nullable()->after('area_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('area_id');
            $table->dropColumn(['role', 'pledge_accepted_at']);
        });
    }
};
