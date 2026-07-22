<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 5)->unique();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE queue MODIFY COLUMN type VARCHAR(5) NOT NULL DEFAULT 'A'");

        DB::table('queue_types')->insert([
            ['name' => 'Inquiry', 'code' => 'A', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Payments', 'code' => 'P', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Others', 'code' => 'O', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_types');
    }
};
