<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // MongoDB is schemaless. Backfill existing documents while the model
        // defaults keep older records safe if this migration has not run yet.
        User::whereNull('role')->update(['role' => 'student']);
        User::whereNull('status')->update(['status' => 'active']);
    }

    public function down(): void
    {
        // Role and status are intentionally retained on rollback because the
        // fields are part of the user document rather than a Mongo schema.
    }
};
