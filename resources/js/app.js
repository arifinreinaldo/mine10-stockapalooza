// Import Alpine.js and plugins
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import persist from '@alpinejs/persist';

// Register Alpine.js plugins
Alpine.plugin(collapse);
Alpine.plugin(persist);

// Import stores
import './stores/language';
import './stores/dashboard';
import './stores/favorites';

// Start Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Import ApexCharts
import ApexCharts from 'apexcharts';
window.ApexCharts = ApexCharts;
