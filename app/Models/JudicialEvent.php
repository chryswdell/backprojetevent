<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JudicialEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'date_evenement',
        'infractions',
        'saisine',
        'partie_civile_identites',
        'partie_civile_pv_numero',
        'partie_civile_pv_reference',
        'mis_en_cause_identites',
        'mis_en_cause_pv_numero',
        'mis_en_cause_pv_reference',
        'observation',
        'resultat',
        'photo_path',
    ];

    protected $casts = [
        'date_evenement' => 'date',
    ];

    protected $appends = [
        'photo_url',
    ];

    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo_path) {
            return null;
        }

        // renvoie une URL absolue vers /storage/...
        return asset('storage/' . $this->photo_path);
    }
}
