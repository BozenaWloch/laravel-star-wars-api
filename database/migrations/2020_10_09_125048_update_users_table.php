<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('last_name')->after('name');
            $table->renameColumn('name', 'first_name');
            $table->string('email')->nullable()->default(null)->change();
            $table->string('nick_name')->after('last_name')->nullable()->default(null)->unique();
            $table->integer('role')->after('email');
            $table->boolean('is_blocked')->after('role')->default(false);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->renameColumn('first_name', 'name');
            $table->dropColumn('last_name');
            $table->dropColumn('nick_name');
            $table->dropColumn('role');
            $table->dropColumn('is_blocked');
            $table->dropSoftDeletes();
        });
    }
}
