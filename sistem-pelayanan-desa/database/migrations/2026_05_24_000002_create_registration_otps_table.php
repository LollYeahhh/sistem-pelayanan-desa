<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("registration_otps", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->string("email");
            $table->string("code_hash");
            $table->unsignedTinyInteger("attempts")->default(0);
            $table->timestamp("expires_at");
            $table->timestamp("verified_at")->nullable();
            $table->timestamp("last_sent_at")->nullable();
            $table->timestamps();

            $table->index(["user_id", "verified_at"]);
            $table->index("expires_at");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("registration_otps");
    }
};
