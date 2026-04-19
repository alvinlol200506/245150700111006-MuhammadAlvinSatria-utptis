<?php

namespace App\Models;

use Illuminate\Support\Facades\File;

class Manga
{
    /**
     * Path ke file JSON sebagai mock database.
     */
    protected static function path(): string
    {
        return database_path('data/manga.json');
    }

    /**
     * Ambil semua manga dari file JSON.
     */
    public static function all(): array
    {
        $path = self::path();

        if (!File::exists($path)) {
            return [];
        }

        $content = File::get($path);
        $data = json_decode($content, true);

        return is_array($data) ? $data : [];
    }

    /**
     * Cari manga berdasarkan id. Return null jika tidak ditemukan.
     */
    public static function find(int $id): ?array
    {
        foreach (self::all() as $item) {
            if ((int) $item['id'] === $id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Simpan kembali array manga ke file JSON.
     */
    public static function save(array $items): bool
    {
        $json = json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return File::put(self::path(), $json) !== false;
    }

    /**
     * Hitung id berikutnya (auto increment).
     */
    public static function nextId(): int
    {
        $items = self::all();

        if (empty($items)) {
            return 1;
        }

        $ids = array_map(fn($item) => (int) $item['id'], $items);
        return max($ids) + 1;
    }

    /**
     * Tambah manga baru.
     */
    public static function create(array $data): array
    {
        $items = self::all();

        $newItem = array_merge(
            ['id' => self::nextId()],
            $data
        );

        $items[] = $newItem;
        self::save($items);

        return $newItem;
    }

    /**
     * Update manga berdasarkan id. Return data terbaru, atau null jika tidak ada.
     */
    public static function update(int $id, array $data): ?array
    {
        $items = self::all();
        $updated = null;

        foreach ($items as $i => $item) {
            if ((int) $item['id'] === $id) {
                $items[$i] = array_merge($item, $data, ['id' => $id]);
                $updated = $items[$i];
                break;
            }
        }

        if ($updated === null) {
            return null;
        }

        self::save($items);
        return $updated;
    }

    /**
     * Hapus manga berdasarkan id. Return true jika berhasil.
     */
    public static function delete(int $id): bool
    {
        $items = self::all();
        $found = false;

        $filtered = array_values(array_filter($items, function ($item) use ($id, &$found) {
            if ((int) $item['id'] === $id) {
                $found = true;
                return false;
            }
            return true;
        }));

        if (!$found) {
            return false;
        }

        self::save($filtered);
        return true;
    }
}
