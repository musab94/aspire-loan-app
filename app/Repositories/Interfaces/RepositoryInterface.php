<?php

namespace App\Repositories\Interfaces;

interface RepositoryInterface
{
    public function create($data);

    public function update($data, $id);

}
