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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('donor_name')->nullable();
            $table->string('donor_phone', 20);
            $table->foreignId('donor_area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->foreignId('target_area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->enum('donation_scope', ['own_area', 'selected_area', 'most_needed']);
            $table->enum('donation_type', ['meat_kg', 'money', 'sacrifice_share', 'full_sacrifice']);
            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('meat_kg', 10, 2)->nullable();
            $table->text('pickup_address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('location_accuracy')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'received', 'allocated', 'in_distribution', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
