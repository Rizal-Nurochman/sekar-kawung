import Alpine from 'alpinejs';
import { navigation } from './modules/navigation.js';
import { catalog } from './modules/catalog.js';
import { productModal } from './modules/product-modal.js';
import { calculator } from './modules/calculator.js';
import { adminCalculator } from './modules/admin-calculator.js';

window.Alpine = Alpine;

Alpine.data('navigation', navigation);
Alpine.data('catalog', catalog);
Alpine.data('productModal', productModal);
Alpine.data('calculator', calculator);
Alpine.data('adminCalculator', adminCalculator);

Alpine.start();
