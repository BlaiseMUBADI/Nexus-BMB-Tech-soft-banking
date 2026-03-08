<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

/**
 * @property int                        $id
 * @property string                     $name
 * @property string                     $email
 * @property string|null                $agent_matricule
 * @property string|null                $etat
 * @property \App\Models\Agent|null     $agent
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'etat',
        'agent_matricule',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    /** Dossier agent lié à ce compte utilisateur */
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_matricule', 'matricule');
    }

    // -------------------------------------------------------------------------
    // RBAC dynamique — cache mémoire (par requête HTTP)
    // -------------------------------------------------------------------------

    /** Cache des codes de rôles pour cet utilisateur (durée : 1 requête) */
    private ?array $_roleCodesCache = null;

    /** Cache des codes de permissions pour cet utilisateur (durée : 1 requête) */
    private ?array $_permCodesCache = null;

    /**
     * Indique si cet utilisateur est super-administrateur (rôle EBEN-ROL1).
     * Un super-admin a accès à TOUT sans restriction.
     */
    public function isAdmin(): bool
    {
        return in_array('EBEN-ROL1', $this->getRoleCodes(), true);
    }

    /**
     * Retourne tous les codes de rôles de l'utilisateur.
     * Résultat mis en cache mémoire pour éviter des requêtes SQL répétées.
     *
     * @return string[]
     */
    public function getRoleCodes(): array
    {
        if ($this->_roleCodesCache === null) {
            $this->_roleCodesCache = DB::table('tb_role_user')
                ->where('user_id', $this->id)
                ->pluck('role_code')
                ->toArray();
        }

        return $this->_roleCodesCache;
    }

    /**
     * Retourne tous les codes de permissions hérités via les rôles.
     * Résultat mis en cache mémoire pour éviter des requêtes SQL répétées.
     *
     * @return string[]
     */
    public function getPermissionCodes(): array
    {
        if ($this->_permCodesCache === null) {
            $roleCodes = $this->getRoleCodes();

            if (empty($roleCodes)) {
                $this->_permCodesCache = [];
            } elseif (in_array('EBEN-ROL1', $roleCodes, true)) {
                // Super-admin : retourne TOUTES les permissions de la DB
                // → le sidebar affiche tout, aucune route ne peut bloquer l'admin
                $this->_permCodesCache = DB::table('tb_permissions')
                    ->pluck('code')
                    ->toArray();
            } else {
                $this->_permCodesCache = DB::table('tb_role_permission')
                    ->whereIn('role_code', $roleCodes)
                    ->pluck('permission_code')
                    ->unique()
                    ->toArray();
            }
        }

        return $this->_permCodesCache;
    }

    /**
     * Vérifie si l'utilisateur possède un code de permission donné.
     * Accepte un ou plusieurs codes (OR logique si tableau).
     *
     * @param  string|string[]  $code
     */
    public function hasPermission(string|array $code): bool
    {
        // Super-admin bypass : EBEN-ROL1 passe toujours
        if ($this->isAdmin()) {
            return true;
        }

        $userPerms = $this->getPermissionCodes();

        foreach ((array) $code as $c) {
            if (in_array($c, $userPerms, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si l'utilisateur possède un code de rôle donné.
     *
     * @param  string|string[]  $code
     */
    public function hasRole(string|array $code): bool
    {
        $userRoles = $this->getRoleCodes();

        foreach ((array) $code as $c) {
            if (in_array($c, $userRoles, true)) {
                return true;
            }
        }

        return false;
    }
}

