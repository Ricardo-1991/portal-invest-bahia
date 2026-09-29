<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->char('currency', 3)->default('BRL')->after('price');
            $table->string('area_unit')->default('ha')->after('area');
            $table->decimal('area_sqm', 20, 2)->nullable()->after('area_unit')->index();
        });

        DB::table('listings')->whereNotNull('area')->update([
            'area_sqm' => DB::raw('area * 10000'),
        ]);
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['currency', 'area_unit', 'area_sqm']);
        });
    }
};
