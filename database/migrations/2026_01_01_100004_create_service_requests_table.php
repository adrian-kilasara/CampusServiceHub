<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('student_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->nullOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->text('description');
            $table->enum('urgency', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', [
                'pending', 'accepted', 'in_progress', 'completed', 'cancelled', 'disputed'
            ])->default('pending');
            $table->json('files')->nullable();
            $table->decimal('quoted_price', 10, 2)->nullable();
            $table->decimal('final_price', 10, 2)->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
