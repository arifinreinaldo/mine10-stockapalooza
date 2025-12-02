<button
    @click="$store.language.toggle()"
    class="p-2 hover:bg-dark-light rounded-lg transition-colors tap-target flex items-center gap-2"
    :aria-label="'Switch to ' + ($store.language.current === 'id' ? 'English' : 'Indonesia')">
    <span class="text-2xl" x-text="$store.language.getFlag()"></span>
    <span class="hidden md:inline text-sm font-medium" x-text="$store.language.getName()"></span>
</button>
