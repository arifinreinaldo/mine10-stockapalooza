import Alpine from 'alpinejs';

Alpine.store('favorites', {
    items: [],
    count: 0,
    loading: false,
    error: null,

    // Initialize - load favorites from API
    async init() {
        await this.load();
    },

    // Load favorites from API
    async load() {
        this.loading = true;
        this.error = null;

        try {
            const response = await fetch('/api/favorites', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            if (!response.ok) {
                throw new Error('Failed to load favorites');
            }

            const result = await response.json();

            if (result.success) {
                this.items = result.data || [];
                this.count = result.count || this.items.length;
            } else {
                this.error = result.message || 'Failed to load favorites';
            }
        } catch (err) {
            this.error = err.message;
            console.error('Error loading favorites:', err);
        } finally {
            this.loading = false;
        }
    },

    // Add to favorites
    async add(symbol, name) {
        this.loading = true;
        this.error = null;

        try {
            const response = await fetch('/api/favorites', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ symbol, name })
            });

            if (!response.ok) {
                throw new Error('Failed to add favorite');
            }

            const result = await response.json();

            if (result.success) {
                // Reload favorites list
                await this.load();

                // Dispatch event
                window.dispatchEvent(new CustomEvent('favorite-added', {
                    detail: { symbol, name }
                }));

                return true;
            } else {
                this.error = result.message || 'Failed to add favorite';
                return false;
            }
        } catch (err) {
            this.error = err.message;
            console.error('Error adding favorite:', err);
            return false;
        } finally {
            this.loading = false;
        }
    },

    // Remove from favorites
    async remove(symbol) {
        this.loading = true;
        this.error = null;

        try {
            const response = await fetch(`/api/favorites/${symbol}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            if (!response.ok) {
                throw new Error('Failed to remove favorite');
            }

            const result = await response.json();

            if (result.success) {
                // Reload favorites list
                await this.load();

                // Dispatch event
                window.dispatchEvent(new CustomEvent('favorite-removed', {
                    detail: { symbol }
                }));

                return true;
            } else {
                this.error = result.message || 'Failed to remove favorite';
                return false;
            }
        } catch (err) {
            this.error = err.message;
            console.error('Error removing favorite:', err);
            return false;
        } finally {
            this.loading = false;
        }
    },

    // Check if a symbol is favorited
    isFavorite(symbol) {
        return this.items.some(item => item.symbol === symbol);
    },

    // Toggle favorite
    async toggle(symbol, name) {
        if (this.isFavorite(symbol)) {
            return await this.remove(symbol);
        } else {
            return await this.add(symbol, name);
        }
    }
});
