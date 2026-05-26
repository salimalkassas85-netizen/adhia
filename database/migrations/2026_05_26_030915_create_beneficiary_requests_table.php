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
        Schema::create('beneficiary_requests', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('first_name', 50);
            $table->string('phone', 20);
            $table->foreignId('area_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('family_members_count')->nullable();
            $table->boolean('has_children')->default(false);
            $table->boolean('has_elderly')->default(false);
            $table->text('full_address');
            $table->string('landmark')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedInteger('location_accuracy')->nullable();
            $table->enum('status', ['pending', 'approved', 'assigned', 'gift_received_by_agent', 'on_the_way', 'delivered', 'failed', 'cancelled'])->default('pending');
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('agent_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiary_requests');
    }
};
