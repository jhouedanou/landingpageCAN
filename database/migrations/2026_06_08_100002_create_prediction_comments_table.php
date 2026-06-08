<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prediction_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prediction_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_moderated')->default(false);
            $table->timestamps();

            $table->index(['prediction_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prediction_comments');
    }
};
