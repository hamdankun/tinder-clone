<?php

namespace App\Repositories\Contracts;

interface PictureRepositoryContract
{
    public function create(array $data);
    
    public function findById(int $id);
    
    public function getByUserId(int $userId);
    
    public function delete(int $id): bool;
    
    public function update(int $id, array $data);
}
