<?php

namespace App\Http\Controllers\Profil;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\RH\Agent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Affiche le profil (redirige vers edit).
     */
    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('profile.edit');
    }

    /**
     * Affiche le formulaire de profil complet.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $agent = $user->agent;

        // Affectations de l'agent avec poste + service
        $affectations = $agent
            ? \App\Models\RH\Affectation::where('agent_matricule', $agent->matricule)
                ->with(['poste.service'])
                ->orderByDesc('date_debut')
                ->get()
            : collect();

        // Poste et service courants (via affectation active)
        $activeAff = $affectations->firstWhere('Etat', 'Actif');
        $poste   = $activeAff?->poste   ?? null;
        $service = $poste?->service     ?? null;

        // Rôles et permissions
        $userRoles = DB::table('tb_role_user')
            ->where('user_id', $user->id)
            ->pluck('role_code');

        $userPermissions = DB::table('tb_role_permission')
            ->whereIn('role_code', $userRoles)
            ->pluck('permission_code');

        $roles       = \App\Models\RH\Role::orderBy('nom')->get();
        $permissions = \App\Models\RH\Permission::orderBy('nom')->get();

        return view('profile.edit', compact(
            'user', 'agent', 'affectations',
            'poste', 'service',
            'roles', 'permissions',
            'userRoles', 'userPermissions'
        ));
    }

    /**
     * Sert la photo de l'agent CONNECTÉ (voir le commentaire de la route
     * profile.photo) — jamais celle d'un autre agent, ce qui rend inutile
     * toute vérification de permission RH ici.
     */
    public function photo(Request $request, $filename)
    {
        $user = $request->user();
        $agent = $user->agent;

        if (!$agent || !$agent->photo || basename($agent->photo) !== $filename) {
            abort(404);
        }

        // base_path (pas public_path) : même convention que AgentController::photo()
        // (utilisé par la barre de navigation et le module RH) — les deux DOIVENT
        // pointer vers le même dossier physique sous peine d'images introuvables.
        $path = base_path('images_projet/' . $agent->photo);
        if (!file_exists($path)) {
            abort(404);
        }

        if (ob_get_level()) {
            ob_end_clean();
        }
        return response()->file($path, [
            'Content-Type' => mime_content_type($path),
            'X-Content-Type-Options' => 'nosniff',
            'Content-Length' => filesize($path),
        ]);
    }

    /**
     * Met à jour les informations du compte (nom + email), le mot de passe
     * ou la photo de profil.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // ── Changement de mot de passe ─────────────────────────
        if ($request->filled('_change_password')) {
            $request->validate([
                'current_password'      => ['required', 'current_password'],
                'password'              => ['required', 'string', 'min:8', 'confirmed'],
            ], [
                'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
                'password.min'                      => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
                'password.confirmed'                => 'La confirmation ne correspond pas.',
            ]);

            $user->password = Hash::make($request->password);
            $user->save();

            return redirect()->route('profile.edit')->with('status', 'password-updated');
        }

        // ── Changement de photo ─────────────────────────────────
        // Le fichier reçu ici est déjà cadré/redimensionné en carré côté
        // navigateur (Cropper.js, cf. profile/edit.blade.php) : le
        // redimensionnement GD ci-dessous ne fait plus que garantir une
        // taille maximale raisonnable (400px) et re-compresser en JPEG,
        // même si le JS a été contourné (repli robuste).
        if ($request->filled('_change_photo')) {
            $request->validate([
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            ]);

            $agent = $user->agent;
            if (!$agent) {
                return redirect()->route('profile.edit')->with('error', 'Aucun dossier agent lié à ce compte.');
            }

            if ($request->hasFile('photo')) {
                try {
                    $image = $request->file('photo');

                    $srcPath = $image->getRealPath();
                    $info = getimagesize($srcPath);
                    if ($info === false) {
                        throw new \Exception('Fichier image invalide.');
                    }
                    [$width, $height] = $info;
                    $maxDim = 400;
                    $ratio = min($maxDim / $width, $maxDim / $height, 1);
                    $newWidth = (int) ($width * $ratio);
                    $newHeight = (int) ($height * $ratio);

                    switch ($info[2]) {
                        case IMAGETYPE_JPEG:
                            $srcImg = imagecreatefromjpeg($srcPath);
                            break;
                        case IMAGETYPE_PNG:
                            $srcImg = imagecreatefrompng($srcPath);
                            break;
                        case IMAGETYPE_GIF:
                            $srcImg = imagecreatefromgif($srcPath);
                            break;
                        case IMAGETYPE_WEBP:
                            $srcImg = function_exists('imagecreatefromwebp') ? imagecreatefromwebp($srcPath) : false;
                            if (!$srcImg) {
                                throw new \Exception('Format WebP non supporté par ce serveur.');
                            }
                            break;
                        default:
                            throw new \Exception("Format d'image non supporté.");
                    }

                    $dstImg = imagecreatetruecolor($newWidth, $newHeight);
                    imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                    $imageName = time() . '_' . uniqid() . '.jpg';
                    // base_path (pas public_path) : même dossier physique que
                    // AgentController::photo() (barre de navigation, module RH) —
                    // sinon la photo s'enregistre mais reste invisible partout
                    // ailleurs que sur la page Profil elle-même.
                    $destinationPath = base_path('images_projet/agents');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    $savePath = $destinationPath . DIRECTORY_SEPARATOR . $imageName;
                    imagejpeg($dstImg, $savePath, 85);
                    imagedestroy($srcImg);
                    imagedestroy($dstImg);

                    // Verrou sur la ligne agent pendant la bascule ancienne/nouvelle
                    // photo, pour éviter toute incohérence en cas de double-soumission.
                    DB::transaction(function () use ($agent, $imageName) {
                        $lockedAgent = Agent::where('matricule', $agent->matricule)->lockForUpdate()->first();
                        if ($lockedAgent && $lockedAgent->photo && file_exists(base_path('images_projet/' . $lockedAgent->photo))) {
                            @unlink(base_path('images_projet/' . $lockedAgent->photo));
                        }
                        $lockedAgent?->update(['photo' => 'agents/' . $imageName]);
                    });
                } catch (\Exception $e) {
                    Log::error('Erreur upload photo profil: ' . $e->getMessage());
                    return back()->withErrors(['photo' => "Erreur lors de l'upload de la photo : " . $e->getMessage()])->withInput();
                }
            }

            return redirect()->route('profile.edit')->with('status', 'photo-updated');
        }

        // ── Mise à jour nom + email ────────────────────────────
        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255',
                        \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->fill($request->only('name', 'email'));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Supprime le compte utilisateur.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
