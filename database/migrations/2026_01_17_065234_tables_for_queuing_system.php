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
        Schema::create('queue', function (Blueprint $table) {
            $table->id();
            $table->text("number");
            $table->enum("type", ["A","B", "C"])->default("A");
            $table->enum('status', ["ready","pending",'done','reserved'])->default("ready");
            $table->timestamps();
        });

        Schema::create('windows', function (Blueprint $table) {
            $table->id();
            $table->string("window_name")->default("Window");
            $table->enum("status", ["online","offline"])->default("online");
            $table->foreignId('queue_ticket')->nullable()->constrained('queue');
            $table->boolean("isCalling")->default(0);
            $table->timestamps();
        });

        // this has become useless now
        // Schema::create('taken_task', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('queue_id')->constrained('queue')->onDelete('cascade');
        //     $table->foreignId('window_id')->constrained('windows')->onDelete('cascade');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
