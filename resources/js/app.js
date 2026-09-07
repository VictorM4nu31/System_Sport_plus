import './bootstrap';
import { initCatalogo } from './catalogo';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initCatalogo();
});
