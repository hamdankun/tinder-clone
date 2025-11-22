<?php

namespace App\Repositories\Implementations;

use App\Models\Picture;
use App\Repositories\Contracts\PictureRepositoryContract;

class PictureRepository implements PictureRepositoryContract
{
    public function create(array $data)
    {
        return Picture::create($data);
    }

    public function findById(int $id)
    {
        return Picture::find($id);
    }

    public function getByUserId(int $userId)
    {
        return Picture::where('user_id', $userId)
            ->orderBy('order')
            ->get();
    }

    public function delete(int $id): bool
    {
        return $this->findById($id)?->delete() ?? false;
    }

    public function update(int $id, array $data)
    {
        $picture = $this->findById($id);
        $picture->update($data);
        return $picture;
    }
}
