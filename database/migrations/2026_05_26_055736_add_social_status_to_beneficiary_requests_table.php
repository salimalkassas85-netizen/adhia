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
        Schema::table('beneficiary_requests', function (Blueprint $table) {
            $table->string('social_status')->nullable()->after('has_elderly');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiary_requests', function (Blueprint $table) {
            $table->dropColumn('social_status');
        });
    }
};
