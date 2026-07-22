<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('video_list', function (Blueprint $table) {
            $table->string('original_name')->after('video_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('video_list', function (Blueprint $table) {
            $table->dropColumn('original_name');
        });
    }
};
