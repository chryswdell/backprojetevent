<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct()
    {
        // Toutes les actions nécessitent un utilisateur connecté
        $this->middleware('auth:sanctum');
    }

    /**
     * Vérifie que l'utilisateur connecté est admin.
     */
    protected function ensureAdmin(Request $request): void
    {
        $user = $request->user();

        if (! $user || ! $user->is_admin) {
            abort(Response::HTTP_FORBIDDEN, 'Accès réservé aux administrateurs.');
        }
    }

    /**
     * GET /api/v1/users
     * Liste des utilisateurs (admin uniquement)
     */
    public function index(Request $request)
    {
        $this->ensureAdmin($request);

        $users = User::orderBy('name')->get([
            'id',
            'name',
            'email',
            'is_admin',
            'created_at',
        ]);

        return response()->json($users);
    }

    /**
     * POST /api/v1/users
     * Création d'un utilisateur (admin uniquement)
     */
    public function store(Request $request)
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'is_admin' => ['sometimes', 'boolean'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => $data['is_admin'] ?? false,
        ]);

        return response()->json($user, Response::HTTP_CREATED);
    }

    /**
     * GET /api/v1/users/{user}
     */
    public function show(Request $request, User $user)
    {
        $this->ensureAdmin($request);

        return response()->json([
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'is_admin'   => $user->is_admin,
            'created_at' => $user->created_at,
        ]);
    }

    /**
     * PUT/PATCH /api/v1/users/{user}
     */
    public function update(Request $request, User $user)
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'name'     => ['sometimes', 'string', 'max:255'],
            'email'    => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['sometimes', 'nullable', 'string', 'min:6'],
            'is_admin' => ['sometimes', 'boolean'],
        ]);

        if (array_key_exists('name', $data)) {
            $user->name = $data['name'];
        }

        if (array_key_exists('email', $data)) {
            $user->email = $data['email'];
        }

        if (array_key_exists('is_admin', $data)) {
            $user->is_admin = $data['is_admin'];
        }

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return response()->json($user);
    }

    /**
     * DELETE /api/v1/users/{user}
     */
    public function destroy(Request $request, User $user)
    {
        $this->ensureAdmin($request);

        $user->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
