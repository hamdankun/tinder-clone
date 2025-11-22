<?php

namespace App\Repositories\Implementations;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryContract;

class UserRepository implements UserRepositoryContract
{
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function getRecommendedPeople(int $userId, int $page = 1, int $perPage = 10): array
    {
        $paginator = User::where('id', '!=', $userId)
            ->whereNotIn('id', function ($query) use ($userId) {
                $query->select('to_user_id')
                    ->from('likes')
                    ->where('from_user_id', $userId);
            })
            ->whereNotIn('id', function ($query) use ($userId) {
                $query->select('to_user_id')
                    ->from('dislikes')
                    ->where('from_user_id', $userId);
            })
            ->with('pictures')
            ->withCount('likedBy')
            ->orderByDesc('liked_by_count')
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = $this->findById($id);
        $user->update($data);
        return $user;
    }

    public function delete(int $id): bool
    {
        return $this->findById($id)?->delete() ?? false;
    }
}
