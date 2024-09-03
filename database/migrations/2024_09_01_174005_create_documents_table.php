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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->string('type_1')->nullable(); // Type for the first document
            $table->string('path_1')->nullable(); // Path for the first document
            $table->string('type_2')->nullable(); // Type for the second document
            $table->string('path_2')->nullable(); // Path for the second document
            $table->string('type_3')->nullable(); // Type for the third document
            $table->string('path_3')->nullable(); // Path for the third document
            $table->string('type_4')->nullable(); // Type for the fourth document
            $table->string('path_4')->nullable(); // Path for the fourth document
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
