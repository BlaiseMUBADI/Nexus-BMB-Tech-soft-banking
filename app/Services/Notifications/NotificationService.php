<?php

namespace App\Services\Notifications;

use App\Models\User;
use App\Notifications\SystemDatabaseNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function resolveCategory(string $title, string $message, array $options = []): string
    {
        $category = $options['category'] ?? null;
        if (is_string($category) && $category !== '') {
            return $category;
        }

        $haystack = strtolower(implode(' ', array_filter([
            $title,
            $message,
            (string) ($options['action_url'] ?? ''),
            (string) ($options['action_label'] ?? ''),
        ])));

        return match (true) {
            str_contains($haystack, 'credit') => 'credit',
            str_contains($haystack, 'tresorerie') || str_contains($haystack, 'coffre') || str_contains($haystack, 'ravitail') => 'tresorerie',
            str_contains($haystack, 'guichet') || str_contains($haystack, 'caisse') || str_contains($haystack, 'operation') || str_contains($haystack, 'dotation') || str_contains($haystack, 'reversement') => 'caisse',
            str_contains($haystack, 'role') || str_contains($haystack, 'permission') || str_contains($haystack, 'administration') || str_contains($haystack, 'autorisation') => 'administration',
            default => 'systeme',
        };
    }

    public function buildOptions(string $title, string $message, array $options = []): array
    {
        $options['category'] = $this->resolveCategory($title, $message, $options);

        return $options;
    }

    public function notifyUser(
        User $user,
        string $title,
        string $message,
        array $options = []
    ): void {
        try {
            $user->notify(new SystemDatabaseNotification($title, $message, $this->buildOptions($title, $message, $options)));
        } catch (\Throwable $e) {
            Log::warning('Impossible d\'envoyer une notification utilisateur.', [
                'user_id' => $user->id,
                'title' => $title,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param  Collection<int, User>|array<int, User>  $users
     */
    public function notifyUsers(
        Collection|array $users,
        string $title,
        string $message,
        array $options = []
    ): void {
        foreach (collect($users)->unique('id') as $user) {
            if ($user instanceof User) {
                $this->notifyUser($user, $title, $message, $options);
            }
        }
    }

    /**
     * @return Collection<int, User>
     */
    public function usersWithPermission(string $permissionCode): Collection
    {
        $userIds = DB::table('tb_role_user as ru')
            ->join('tb_role_permission as rp', 'rp.role_code', '=', 'ru.role_code')
            ->where('rp.permission_code', $permissionCode)
            ->distinct()
            ->pluck('ru.user_id');

        return User::query()
            ->whereIn('id', $userIds)
            ->get();
    }

    /**
     * @return Collection<int, User>
     */
    public function usersWithRole(string $roleCode): Collection
    {
        $userIds = DB::table('tb_role_user')
            ->where('role_code', $roleCode)
            ->pluck('user_id');

        return User::query()
            ->whereIn('id', $userIds)
            ->get();
    }

    public function notifyUsersWithPermission(
        string $permissionCode,
        string $title,
        string $message,
        array $options = []
    ): void {
        $this->notifyUsers($this->usersWithPermission($permissionCode), $title, $message, $options);
    }

    public function notifyUsersWithRole(
        string $roleCode,
        string $title,
        string $message,
        array $options = []
    ): void {
        $this->notifyUsers($this->usersWithRole($roleCode), $title, $message, $options);
    }

    /**
     * @param  string[]  $matricules
     * @return Collection<int, User>
     */
    public function usersByAgentMatricules(array $matricules): Collection
    {
        $matricules = array_values(array_filter(array_unique($matricules)));
        if (empty($matricules)) {
            return collect();
        }

        return User::query()
            ->whereIn('agent_matricule', $matricules)
            ->get();
    }

    public function notifyAgentMatricules(
        array $matricules,
        string $title,
        string $message,
        array $options = []
    ): void {
        $this->notifyUsers($this->usersByAgentMatricules($matricules), $title, $message, $options);
    }
}
