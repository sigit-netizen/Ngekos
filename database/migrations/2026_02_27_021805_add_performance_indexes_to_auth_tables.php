<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Contoh implementasi best-practice indexing sesuai request "ensure_proper_database_indexing"
        // Laravel's default users table already has unique('email'), but just as an optimization example:
        Schema::table('users', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes('users');
            if (!array_key_exists('users_email_index', $indexesFound) && !array_key_exists('users_email_unique', $indexesFound)) {
                $table->index('email');
            }
        });

        // Add compound index for 'model_has_roles' if it exists and is relevant
        // Assuming 'model_has_roles' is a pivot table with 'model_id', 'model_type', and 'role_id'
        if (Schema::hasTable('model_has_roles')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexesFound = $sm->listTableIndexes('model_has_roles');
                // Check if the compound index already exists
                if (!array_key_exists('model_has_roles_model_id_model_type_role_id_index', $indexesFound)) {
                    $table->index(['model_id', 'model_type', 'role_id'], 'model_has_roles_model_id_model_type_role_id_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the email index if it was added by this migration
        Schema::table('users', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes('users');
            if (array_key_exists('users_email_index', $indexesFound)) {
                $table->dropIndex('users_email_index');
            }
        });

        // Drop the compound index for 'model_has_roles' if it was added by this migration
        if (Schema::hasTable('model_has_roles')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexesFound = $sm->listTableIndexes('model_has_roles');
                if (array_key_exists('model_has_roles_model_id_model_type_role_id_index', $indexesFound)) {
                    $table->dropIndex('model_has_roles_model_id_model_type_role_id_index');
                }
            });
        }
    }
};
