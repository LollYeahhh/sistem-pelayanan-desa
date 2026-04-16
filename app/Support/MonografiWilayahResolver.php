<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MonografiWilayahResolver
{
    public function decorateRtUsers(Collection $rtUsers): Collection
    {
        return $rtUsers->values()->map(function (User $user, int $index) {
            $label = $this->resolveForUser($user, $index + 1);
            $user->rt_label = $label;
            $user->wilayah_label = $label;

            return $user;
        });
    }

    public function labelsByUserId(Collection $rtUsers): array
    {
        return $this->decorateRtUsers($rtUsers)
            ->mapWithKeys(fn(User $user) => [$user->id => $user->wilayah_label])
            ->all();
    }

    public function resolveForUser(User $user, int $position): string
    {
        if (filled($user->rt_rw)) {
            return "RT. " . Str::upper(trim((string) $user->rt_rw));
        }

        return "RT " . str_pad((string) $position, 2, "0", STR_PAD_LEFT);
    }
}
