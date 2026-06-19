<?php

namespace App\Providers;

use App\Repositories\BookPaymentRepository;
use App\Repositories\BookingRepository;
use App\Repositories\BookReceiptRepository;
use App\Repositories\CertificateRepository;
use App\Repositories\Contracts\BookPaymentRepositoryInterface;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\BookReceiptRepositoryInterface;
use App\Repositories\Contracts\CertificateRepositoryInterface;
use App\Repositories\Contracts\CourseRepositoryInterface;
use App\Repositories\Contracts\EnrolmentRepositoryInterface;
use App\Repositories\Contracts\ManualTransactionRepositoryInterface;
use App\Repositories\Contracts\ModuleRepositoryInterface;
use App\Repositories\Contracts\PaynowRepositoryInterface;
use App\Repositories\Contracts\PaynowTransactionRepositoryInterface;
use App\Repositories\Contracts\PaymentChannelRepositoryInterface;
use App\Repositories\Contracts\StudentRepositoryInterface;
use App\Repositories\Contracts\TrainerRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\CourseRepository;
use App\Repositories\EnrolmentRepository;
use App\Repositories\ManualTransactionRepository;
use App\Repositories\ModuleRepository;
use App\Repositories\PaynowRepository;
use App\Repositories\PaynowTransactionRepository;
use App\Repositories\PaymentChannelRepository;
use App\Repositories\StudentRepository;
use App\Repositories\TrainerRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public array $bindings = [
        StudentRepositoryInterface::class                  => StudentRepository::class,
        CourseRepositoryInterface::class                   => CourseRepository::class,
        EnrolmentRepositoryInterface::class                => EnrolmentRepository::class,
        CertificateRepositoryInterface::class              => CertificateRepository::class,
        TrainerRepositoryInterface::class                  => TrainerRepository::class,
        UserRepositoryInterface::class                     => UserRepository::class,
        ModuleRepositoryInterface::class                   => ModuleRepository::class,
        BookingRepositoryInterface::class                  => BookingRepository::class,
        BookReceiptRepositoryInterface::class               => BookReceiptRepository::class,
        PaynowRepositoryInterface::class                    => PaynowRepository::class,
        PaynowTransactionRepositoryInterface::class        => PaynowTransactionRepository::class,
        BookPaymentRepositoryInterface::class              => BookPaymentRepository::class,
        ManualTransactionRepositoryInterface::class        => ManualTransactionRepository::class,
        PaymentChannelRepositoryInterface::class           => PaymentChannelRepository::class,
    ];

    public function register(): void {}

    public function boot(): void {}
}
