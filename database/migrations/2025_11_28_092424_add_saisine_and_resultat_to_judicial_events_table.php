<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('judicial_events', function (Blueprint $table) {
            // Saisine : texte court (par ex : "Parquet", "Instruction", etc.)
            $table->string('saisine')->nullable()->after('infractions');

            // Résultat : texte court contenant une des valeurs de la liste
            $table->string('resultat')->nullable()->after('observation');
        });
    }

    public function down(): void
    {
        Schema::table('judicial_events', function (Blueprint $table) {
            $table->dropColumn('saisine');
            $table->dropColumn('resultat');
        });
    }
};
