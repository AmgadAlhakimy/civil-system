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
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teams = config('permission.teams');

        if (empty($tableNames)) {
            throw new \Exception(
                'Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.'
            );
        }

        if ($teams && empty($columnNames['team_foreign_key'] ?? null)) {
            throw new \Exception(
                'Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.'
            );
        }

        /*
         * ============================
         * Permissions
         * ============================
         */
        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->string('name_ar')->nullable();

            $table->string('guard_name');

            $table->string('module', 50)->nullable();
            $table->string('action', 50)->nullable();

            $table->text('description')->nullable();

            $table->timestamps();

            $table->unique(['name', 'guard_name']);

            $table->index('module');
            $table->index('action');
        });

        /*
         * ============================
         * Roles
         * ============================
         */
        Schema::create($tableNames['roles'], function (Blueprint $table) use (
            $teams,
            $columnNames
        ) {
            $table->uuid('id')->primary();

            if ($teams || config('permission.testing')) {
                $table->uuid($columnNames['team_foreign_key'])
                    ->nullable();

                $table->index(
                    $columnNames['team_foreign_key'],
                    'roles_team_foreign_key_index'
                );
            }

            $table->string('name');
            $table->string('name_ar')->nullable();

            $table->string('guard_name');

            $table->timestamps();

            if ($teams || config('permission.testing')) {
                $table->unique([
                    $columnNames['team_foreign_key'],
                    'name',
                    'guard_name',
                ]);
            } else {
                $table->unique([
                    'name',
                    'guard_name',
                ]);
            }
        });

        /*
         * ============================
         * Model Has Permissions
         * ============================
         */
        Schema::create(
            $tableNames['model_has_permissions'],
            function (Blueprint $table) use (
                $tableNames,
                $columnNames,
                $teams
            ) {
                $table->uuid('permission_id');

                $table->string('model_type');

                $table->uuid(
                    $columnNames['model_morph_key']
                );

                $table->index(
                    [
                        $columnNames['model_morph_key'],
                        'model_type',
                    ],
                    'model_has_permissions_model_id_model_type_index'
                );

                $table->foreign('permission_id')
                    ->references('id')
                    ->on($tableNames['permissions'])
                    ->onDelete('cascade');

                if ($teams) {
                    $table->uuid(
                        $columnNames['team_foreign_key']
                    );

                    $table->index(
                        $columnNames['team_foreign_key'],
                        'model_has_permissions_team_foreign_key_index'
                    );

                    $table->primary(
                        [
                            $columnNames['team_foreign_key'],
                            'permission_id',
                            $columnNames['model_morph_key'],
                            'model_type',
                        ],
                        'model_has_permissions_permission_model_type_primary'
                    );
                } else {
                    $table->primary(
                        [
                            'permission_id',
                            $columnNames['model_morph_key'],
                            'model_type',
                        ],
                        'model_has_permissions_permission_model_type_primary'
                    );
                }
            }
        );

        /*
         * ============================
         * Model Has Roles
         * ============================
         */
        Schema::create(
            $tableNames['model_has_roles'],
            function (Blueprint $table) use (
                $tableNames,
                $columnNames,
                $teams
            ) {
                $table->uuid('role_id');

                $table->string('model_type');

                $table->uuid(
                    $columnNames['model_morph_key']
                );

                $table->index(
                    [
                        $columnNames['model_morph_key'],
                        'model_type',
                    ],
                    'model_has_roles_model_id_model_type_index'
                );

                $table->foreign('role_id')
                    ->references('id')
                    ->on($tableNames['roles'])
                    ->onDelete('cascade');

                if ($teams) {
                    $table->uuid(
                        $columnNames['team_foreign_key']
                    );

                    $table->index(
                        $columnNames['team_foreign_key'],
                        'model_has_roles_team_foreign_key_index'
                    );

                    $table->primary(
                        [
                            $columnNames['team_foreign_key'],
                            'role_id',
                            $columnNames['model_morph_key'],
                            'model_type',
                        ],
                        'model_has_roles_role_model_type_primary'
                    );
                } else {
                    $table->primary(
                        [
                            'role_id',
                            $columnNames['model_morph_key'],
                            'model_type',
                        ],
                        'model_has_roles_role_model_type_primary'
                    );
                }
            }
        );

        /*
         * ============================
         * Role Has Permissions
         * ============================
         */
        Schema::create(
            $tableNames['role_has_permissions'],
            function (Blueprint $table) use (
                $tableNames
            ) {
                $table->uuid('permission_id');

                $table->uuid('role_id');

                $table->foreign('permission_id')
                    ->references('id')
                    ->on($tableNames['permissions'])
                    ->onDelete('cascade');

                $table->foreign('role_id')
                    ->references('id')
                    ->on($tableNames['roles'])
                    ->onDelete('cascade');

                $table->primary(
                    [
                        'permission_id',
                        'role_id',
                    ],
                    'role_has_permissions_permission_id_role_id_primary'
                );
            }
        );

        /*
         * Clear Permission Cache
         */
        app('cache')
            ->store(
                config('permission.cache.store') !== 'default'
                    ? config('permission.cache.store')
                    : null
            )
            ->forget(
                config('permission.cache.key')
            );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception(
                'Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.'
            );
        }

        Schema::dropIfExists(
            $tableNames['role_has_permissions']
        );

        Schema::dropIfExists(
            $tableNames['model_has_roles']
        );

        Schema::dropIfExists(
            $tableNames['model_has_permissions']
        );

        Schema::dropIfExists(
            $tableNames['roles']
        );

        Schema::dropIfExists(
            $tableNames['permissions']
        );
    }
};
