<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ldap_users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('role')->default('user'); // 👈 default set here
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ldap_users');
    }
};
