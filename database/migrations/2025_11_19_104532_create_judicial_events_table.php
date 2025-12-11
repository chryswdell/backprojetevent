<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('judicial_events', function (Blueprint $table) {
            $table->id(); // clé primaire technique

            // 🔢 Numéro visible dans ton tableau (1,2,3,...) auto-généré
            $table->unsignedInteger('numero')->unique();

            // Colonnes du tableau
            $table->date('date_evenement');            // Date
            $table->text('infractions');               // Infractions

            // Partie civile
            $table->text('partie_civile_identites')->nullable();      // identité(s)
            $table->string('partie_civile_pv_numero')->nullable();    // N°
            $table->string('partie_civile_pv_reference')->nullable(); // procès-verbal (texte / réf)

            // Mise en cause
            $table->text('mis_en_cause_identites')->nullable();       // identité(s)
            $table->string('mis_en_cause_pv_numero')->nullable();     // N°
            $table->string('mis_en_cause_pv_reference')->nullable();  // procès-verbal (texte / réf)

            // Observation
            $table->text('observation')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('judicial_events');
    }
};
