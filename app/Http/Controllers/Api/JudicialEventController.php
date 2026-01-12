<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JudicialEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JudicialEventController extends Controller
{
    public function index(Request $request)
    {
        $query = JudicialEvent::query();

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhere('infractions', 'like', "%{$search}%")
                  ->orWhere('partie_civile_identites', 'like', "%{$search}%")
                  ->orWhere('mis_en_cause_identites', 'like', "%{$search}%");
            });
        }

        return response()->json(
        $query->orderBy('numero', 'asc')->get()
    );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date_evenement'             => ['required', 'date'],
            'infractions'                => ['required', 'string'],

            'saisine'                    => ['nullable', 'string', 'max:255'],

            'partie_civile_identites'    => ['nullable', 'string'],
            'partie_civile_pv_numero'    => ['nullable', 'string', 'max:255'],
            'partie_civile_pv_reference' => ['nullable', 'string', 'max:255'],

            'mis_en_cause_identites'     => ['nullable', 'string'],
            'mis_en_cause_pv_numero'     => ['nullable', 'string', 'max:255'],
            'mis_en_cause_pv_reference'  => ['nullable', 'string', 'max:255'],

            'observation'                => ['nullable', 'string'],
            'resultat'                   => ['nullable', 'string', 'max:255'],

            // nouvelle règle pour la photo
            'photo'                      => ['nullable', 'image', 'max:4096'], // 4 Mo
        ]);

        // Numéro auto
        $lastNumero = JudicialEvent::max('numero');
        $data['numero'] = ($lastNumero ?? 0) + 1;

        //  Upload photo si présente
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('judicial_photos', 'public');
            $data['photo_path'] = $path;
        }

        // on enlève la clé "photo" qui ne correspond pas à une colonne
        unset($data['photo']);

        $event = JudicialEvent::create($data);

        // on renvoie l’event avec photo_url calculée
        return response()->json($event->fresh(), 201);
    }

    public function show(JudicialEvent $judicialEvent)
    {
        return response()->json($judicialEvent);
    }

    public function update(Request $request, JudicialEvent $judicialEvent)
    {
        // Seul l'admin peut modifier
        $user = $request->user();
        if (! $user || ! $user->is_admin) {
            return response()->json([
                'message' => "Vous n'êtes pas autorisé à modifier cet enregistrement.",
            ], 403);
        }

        $data = $request->validate([
            'date_evenement'             => ['required', 'date'],
            'infractions'                => ['required', 'string'],

            'saisine'                    => ['nullable', 'string', 'max:255'],

            'partie_civile_identites'    => ['nullable', 'string'],
            'partie_civile_pv_numero'    => ['nullable', 'string', 'max:255'],
            'partie_civile_pv_reference' => ['nullable', 'string', 'max:255'],

            'mis_en_cause_identites'     => ['nullable', 'string'],
            'mis_en_cause_pv_numero'     => ['nullable', 'string', 'max:255'],
            'mis_en_cause_pv_reference'  => ['nullable', 'string', 'max:255'],

            'observation'                => ['nullable', 'string'],
            'resultat'                   => ['nullable', 'string', 'max:255'],

            // photo optionnelle en update aussi
            'photo'                      => ['nullable', 'image', 'max:4096'],
        ]);

        // si une nouvelle photo est envoyée
        if ($request->hasFile('photo')) {
            // on supprime l’ancienne si elle existe
            if ($judicialEvent->photo_path) {
                Storage::disk('public')->delete($judicialEvent->photo_path);
            }

            $path = $request->file('photo')->store('judicial_photos', 'public');
            $data['photo_path'] = $path;
        }

        unset($data['photo']);

        // On ne touche pas à $judicialEvent->numero
        $judicialEvent->update($data);

        return response()->json($judicialEvent->fresh());
    }

    public function destroy(Request $request, JudicialEvent $judicialEvent)
    {
        // Seul l'admin peut supprimer
        $user = $request->user();
        if (! $user || ! $user->is_admin) {
            return response()->json([
                'message' => "Vous n'êtes pas autorisé à supprimer cet enregistrement.",
            ], 403);
        }

        // on supprime la photo liée si elle existe
        if ($judicialEvent->photo_path) {
            Storage::disk('public')->delete($judicialEvent->photo_path);
        }

        $judicialEvent->delete();

        return response()->json(null, 204);
    }
}
