<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->string('source', 32)->comment('custom | openfoodfacts | ai');
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('barcode')->nullable();
            $table->string('name');
            $table->string('brand')->nullable();

            $table->decimal('kcal', 8, 2)->comment('per 100 g/ml');
            $table->decimal('protein_g', 8, 2)->nullable();
            $table->decimal('carbs_g', 8, 2)->nullable();
            $table->decimal('fat_g', 8, 2)->nullable();
            $table->decimal('fiber_g', 8, 2)->nullable();
            $table->decimal('sugar_g', 8, 2)->nullable();
            $table->decimal('sat_fat_g', 8, 2)->nullable();
            $table->decimal('salt_g', 8, 2)->nullable();

            $table->decimal('serving_size', 8, 2)->nullable();
            $table->string('serving_unit', 16)->nullable();

            $table->boolean('verified')->default(false);
            $table->timestamps();

            $table->index('user_id');
            $table->unique(['source', 'barcode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};
