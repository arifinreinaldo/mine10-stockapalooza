<!-- Favorites Sidebar -->
<div x-show="$store.dashboard.favoritesOpen"
     x-cloak
     @click.self="$store.dashboard.toggleFavorites()"
     class="fixed inset-0 bg-black/50 z-50"
     x-transition:enter="transition-opacity ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     style="display: none;">

    <div @click.stop
         class="fixed right-0 top-0 h-full w-full sm:w-96 bg-dark-light border-l border-dark-lighter
                shadow-2xl overflow-y-auto custom-scrollbar safe-top safe-bottom"
         x-transition:enter="transition-transform ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition-transform ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full">

        <!-- Header -->
        <div class="sticky top-0 bg-dark-light border-b border-dark-lighter p-4 flex items-center justify-between">
            <h2 class="text-xl font-bold" x-text="$store.language.t('myFavorites')"></h2>
            <button @click="$store.dashboard.toggleFavorites()"
                    class="tap-target p-2 hover:bg-dark rounded-lg">
                <span class="text-2xl">&times;</span>
            </button>
        </div>

        <!-- Loading State -->
        <div x-show="$store.favorites.loading" class="p-8 text-center">
            <div class="spinner mx-auto mb-4"></div>
            <p x-text="$store.language.t('loading')"></p>
        </div>

        <!-- Error State -->
        <div x-show="$store.favorites.error" class="p-4">
            <div class="bg-danger/20 border border-danger rounded-lg p-4">
                <p class="text-danger" x-text="$store.favorites.error"></p>
            </div>
        </div>

        <!-- Empty State -->
        <div x-show="!$store.favorites.loading && !$store.favorites.error && $store.favorites.count === 0"
             class="p-8 text-center text-gray-400">
            <p class="text-4xl mb-4">⭐</p>
            <p x-text="$store.language.t('noFavorites')"></p>
        </div>

        <!-- Favorites List -->
        <div x-show="!$store.favorites.loading && !$store.favorites.error && $store.favorites.count > 0"
             class="p-4 space-y-2">
            <template x-for="item in $store.favorites.items" :key="item.symbol">
                <div class="bg-dark rounded-lg p-4 border border-dark-lighter hover:border-primary-500
                            transition-colors cursor-pointer"
                     @click="$store.dashboard.loadStock(item.symbol, item.market); $store.dashboard.toggleFavorites()">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-bold text-lg" x-text="item.symbol"></p>
                            <p class="text-sm text-gray-400" x-text="item.name"></p>
                        </div>
                        <button @click.stop="$store.favorites.remove(item.symbol)"
                                class="p-2 hover:bg-danger/20 rounded-lg transition-colors">
                            <span class="text-danger text-xl">&times;</span>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
