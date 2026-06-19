<?php

namespace App\Services;

use App\Mail\BookingConfirmation;
use App\Models\Booking;
use App\Models\BookingStatusLog;
use App\Models\Course;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Services\Cart\CartService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingService
{
    public function __construct(
        private readonly BookingRepositoryInterface $bookings,
        private readonly CartService $cart,
    ) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->bookings->paginate($perPage, $filters);
    }

    public function get(int $id): Booking
    {
        /** @var Booking $b */
        $b = $this->bookings->findOrFail($id);
        $b->load(['items.course', 'items.schedule.location', 'statusLogs.user', 'assignee']);
        return $b;
    }

    public function findByCode(string $code): ?Booking
    {
        return $this->bookings->findByCode($code);
    }

    /**
     * Create a booking from the customer's details + the cart contents.
     * Does NOT clear the cart — caller is responsible for clearing after
     * confirming the booking is fully committed (payment succeeded etc.).
     * Sends a confirmation email; failures are logged but do not break the flow.
     */
    public function createFromCart(array $customer, ?Request $request = null): Booking
    {
        $cart = $this->cart->detailed();
        if (empty($cart)) {
            throw new \RuntimeException('Your cart is empty.');
        }

        $booking = DB::transaction(function () use ($customer, $cart, $request) {
            /** @var Booking $b */
            $b = $this->bookings->create(array_merge($customer, [
                'booking_code' => $this->bookings->generateBookingCode(),
                'status'       => 'pending',
                'source'       => 'web',
                'ip_address'   => $request?->ip(),
                'user_agent'   => substr((string) $request?->userAgent(), 0, 1024),
            ]));

            foreach ($cart as $row) {
                /** @var Course $course */
                $course = $row['course'];
                $b->items()->create([
                    'course_id'   => $course->id,
                    'schedule_id' => $row['schedule']?->id,
                    'quantity'    => $row['quantity'],
                    'unit_price'  => $course->fee,
                ]);
            }

            BookingStatusLog::create([
                'booking_id' => $b->id,
                'to_status'  => 'pending',
                'comment'    => 'Booking received from website.',
            ]);

            return $b;
        });

        // Email — non-fatal
        try {
            Mail::to($booking->email)->send(new BookingConfirmation($booking));
        } catch (\Throwable $e) {
            Log::warning('Booking confirmation email failed', [
                'booking_id' => $booking->id,
                'error'      => $e->getMessage(),
            ]);
        }

        return $booking->fresh(['items.course', 'items.schedule']);
    }

    /**
     * Clear the cart. Call this only after the booking is fully confirmed
     * (i.e. after payment initiation succeeded for Paynow, or after
     *  booking is saved for manual channels).
     */
    public function clearCart(): void
    {
        $this->cart->clear();
    }

    public function updateStatus(int $id, string $toStatus, ?string $comment = null): Booking
    {
        /** @var Booking $b */
        $b = $this->bookings->findOrFail($id);
        $from = $b->status;

        return DB::transaction(function () use ($b, $from, $toStatus, $comment) {
            $payload = ['status' => $toStatus];
            if ($toStatus === 'contacted' && !$b->contacted_at) $payload['contacted_at'] = now();
            if ($toStatus === 'confirmed' && !$b->confirmed_at) $payload['confirmed_at'] = now();
            $b->update($payload);

            BookingStatusLog::create([
                'booking_id' => $b->id,
                'from_status'=> $from,
                'to_status'  => $toStatus,
                'comment'    => $comment,
                'changed_by' => Auth::id(),
            ]);

            return $b->fresh(['items.course', 'items.schedule', 'statusLogs.user']);
        });
    }

    public function addNote(int $id, string $comment): Booking
    {
        /** @var Booking $b */
        $b = $this->bookings->findOrFail($id);
        BookingStatusLog::create([
            'booking_id' => $b->id,
            'from_status'=> $b->status,
            'to_status'  => $b->status,
            'comment'    => $comment,
            'changed_by' => Auth::id(),
        ]);
        return $b->fresh(['items.course', 'items.schedule', 'statusLogs.user']);
    }

    public function assign(int $id, ?int $userId): Booking
    {
        $b = $this->bookings->update($id, ['assigned_to' => $userId]);
        return $b->fresh(['items.course', 'items.schedule', 'assignee']);
    }
}
