<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FavoritesManager
{
    private const FAVORITES_FILE = 'favorites.json';

    /**
     * Get all favorite tickers
     *
     * @return array
     */
    public function getFavorites(): array
    {
        if (!Storage::exists(self::FAVORITES_FILE)) {
            return [];
        }

        $content = Storage::get(self::FAVORITES_FILE);
        $data = json_decode($content, true);

        return $data ?? [];
    }

    /**
     * Add a ticker to favorites
     *
     * @param string $symbol
     * @param string|null $name
     * @return bool
     */
    public function addFavorite(string $symbol, ?string $name = null): bool
    {
        $favorites = $this->getFavorites();

        // Normalize symbol
        $symbol = strtoupper($symbol);
        if (!str_ends_with($symbol, '.JK')) {
            $symbol .= '.JK';
        }

        // Check if already exists
        foreach ($favorites as $fav) {
            if ($fav['symbol'] === $symbol) {
                return false; // Already in favorites
            }
        }

        // Add new favorite
        $favorites[] = [
            'symbol' => $symbol,
            'name' => $name ?? $symbol,
            'added_at' => now()->toDateTimeString(),
        ];

        return $this->saveFavorites($favorites);
    }

    /**
     * Remove a ticker from favorites
     *
     * @param string $symbol
     * @return bool
     */
    public function removeFavorite(string $symbol): bool
    {
        $favorites = $this->getFavorites();

        $symbol = strtoupper($symbol);
        if (!str_ends_with($symbol, '.JK')) {
            $symbol .= '.JK';
        }

        $filtered = array_filter($favorites, function ($fav) use ($symbol) {
            return $fav['symbol'] !== $symbol;
        });

        // Re-index array
        $filtered = array_values($filtered);

        return $this->saveFavorites($filtered);
    }

    /**
     * Check if ticker is in favorites
     *
     * @param string $symbol
     * @return bool
     */
    public function isFavorite(string $symbol): bool
    {
        $favorites = $this->getFavorites();

        $symbol = strtoupper($symbol);
        if (!str_ends_with($symbol, '.JK')) {
            $symbol .= '.JK';
        }

        foreach ($favorites as $fav) {
            if ($fav['symbol'] === $symbol) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get favorite symbols only
     *
     * @return array
     */
    public function getFavoriteSymbols(): array
    {
        $favorites = $this->getFavorites();
        return array_column($favorites, 'symbol');
    }

    /**
     * Clear all favorites
     *
     * @return bool
     */
    public function clearFavorites(): bool
    {
        return $this->saveFavorites([]);
    }

    /**
     * Save favorites to storage
     *
     * @param array $favorites
     * @return bool
     */
    private function saveFavorites(array $favorites): bool
    {
        $json = json_encode($favorites, JSON_PRETTY_PRINT);
        return Storage::put(self::FAVORITES_FILE, $json);
    }

    /**
     * Get favorites count
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->getFavorites());
    }
}
