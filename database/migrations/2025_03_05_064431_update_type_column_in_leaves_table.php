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
        Schema::table('leaves', function (Blueprint $table) {
            $table->enum('type', [
                'sick',
                'vacation',
                'maternity',
                'paternity',
                'bereavement',
                'solo_parent',
                'special_privilege',
                'study',
                'rehabilitation',
                'special_leave_benefits_for_women',
                'others'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->enum('type', [
                'sick',
                'vacation',
                'maternity',
                'paternity',
                'bereavement',
                'solo_parent',
                'others'
            ])->change();
        });
    }
};
