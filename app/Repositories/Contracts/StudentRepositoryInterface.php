<?php

namespace App\Repositories\Contracts;

interface StudentRepositoryInterface extends BaseRepositoryInterface
{
    public function generateStudentNumber(): string;
}
