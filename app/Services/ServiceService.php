<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ServiceService
{
    public function create(array $data): Service
    {
        return DB::transaction(function () use ($data) {
            $this->applyImage($data);

            return Service::create($data);
        });
    }

    public function update(Service $service, array $data): Service
    {
        return DB::transaction(function () use ($service, $data) {
            $this->applyImage($data, $service);

            $service->update($data);
            return $service->fresh();
        });
    }

    public function delete(Service $service): bool
    {
        return $service->delete();
    }

    private function applyImage(array &$data, ?Service $existing = null): void
    {
        $remove = (bool) ($data['remove_image'] ?? false);
        $file   = $data['image'] ?? null;
        unset($data['image'], $data['remove_image']);

        $disk = config('filesystems.service_image_disk', 'public');

        if ($file instanceof UploadedFile) {
            if ($existing?->image_path) {
                Storage::disk($disk)->delete($existing->image_path);
            }
            $data['image_path'] = $file->store('services', $disk);
            return;
        }

        if ($remove) {
            if ($existing?->image_path) {
                Storage::disk($disk)->delete($existing->image_path);
            }
            $data['image_path'] = null;
        }
    }
}