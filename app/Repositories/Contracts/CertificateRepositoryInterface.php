<?php

namespace App\Repositories\Contracts;

use App\Models\Certificate;

interface CertificateRepositoryInterface extends BaseRepositoryInterface
{
    public function findByNumber(string $number): ?Certificate;
    public function findByVerificationCode(string $code): ?Certificate;
    public function generateCertificateNumber(): string;
    public function generateVerificationCode(): string;
}
