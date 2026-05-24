<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("registration_otps", function (Blueprint $table) {
            $table->dropForeign(["user_id"]);
            $table->dropIndex(["user_id", "verified_at"]);
            $table->unsignedBigInteger("user_id")->nullable()->change();
            $table->foreign("user_id")->references("id")->on("users")->nullOnDelete();
            $table->index(["user_id", "verified_at"]);
        });
    }

    public function down(): void
    {
        Schema::table("registration_otps", function (Blueprint $table) {
            $table->dropForeign(["user_id"]);
            $table->dropIndex(["user_id", "verified_at"]);
            $table->unsignedBigInteger("user_id")->nullable(false)->change();
            $table->foreign("user_id")->references("id")->on("users")->cascadeOnDelete();
            $table->index(["user_id", "verified_at"]);
        });
    }
};
