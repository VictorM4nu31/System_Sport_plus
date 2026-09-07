/**
 * Catálogo instantáneo (mejora progresiva).
 * Solo se activa si existe [data-catalogo] en la página.
 * Sin JS, el formulario GET clásico sigue funcionando (fallback server-side).
 */

const esc = (value) => String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#39;');

function stockBadge(product) {
    if (product.stock <= 0) {
        return '<span class="badge-stock-out">Agotado</span>';
    }
    if (product.stock <= 10) {
        return `<span class="badge-stock-low">¡Últimas ${product.stock}!</span>`;
    }
    return '<span class="badge-stock-ok">En stock</span>';
}

function cardHtml(product, csrf, cartBase) {
    return `
    <article class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 relative">
        <div class="absolute top-2 left-2 z-10 flex gap-2">
            ${product.is_featured ? '<span class="bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded-full">⭐ Destacado</span>' : ''}
            ${stockBadge(product)}
        </div>
        <a href="${esc(product.url)}">
            <img src="${esc(product.image_url)}" alt="${esc(product.name)}" loading="lazy"
                 class="w-full h-48 object-cover hover:scale-105 transition-transform duration-300">
        </a>
        <div class="p-4">
            <p class="text-xs text-gray-500">${esc(product.brand ?? '')}${product.sport_type ? ` · ${esc(product.sport_type)}` : ''}</p>
            <h3 class="text-base font-semibold text-gray-900 truncate">
                <a href="${esc(product.url)}" class="hover:underline">${esc(product.name)}</a>
            </h3>
            <p class="price-mono text-lg font-bold text-gray-900 mt-1">${esc(product.formatted_price)}</p>
            <div class="mt-3 flex gap-2">
                <a href="${esc(product.url)}"
                   class="btn-carbon focus-volt flex-1 text-center text-sm no-underline">Ver detalles</a>
            </div>
            <form action="${esc(cartBase)}/${product.id}/agregar" method="POST" class="mt-2">
                <input type="hidden" name="_token" value="${esc(csrf)}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn-ghost focus-volt w-full text-sm">Agregar al carrito</button>
            </form>
            <div class="mt-2 flex items-center gap-2">
                <a href="${esc(product.url)}" data-quickview="${product.id}"
                   class="focus-volt flex-1 rounded-md border border-line py-2 px-4 text-center text-sm font-semibold text-carbon hover:border-carbon transition-colors">Vista rápida</a>
                <label class="flex cursor-pointer items-center gap-1 rounded-md border border-line py-2 px-3 text-sm font-medium text-carbon hover:border-carbon transition-colors">
                    <input type="checkbox" data-compare="${product.id}" data-name="${esc(product.name)}" class="h-4 w-4 accent-[#131417]">
                    <span>Comparar</span>
                </label>
            </div>
        </div>
    </article>`;
}

export function initCatalogo() {
    const root = document.querySelector('[data-catalogo]');
    if (!root || root.dataset.catalogoInit === '1') {
        return;
    }
    root.dataset.catalogoInit = '1';

    const form = root.querySelector('[data-catalogo-form]');
    const grid = root.querySelector('[data-catalogo-grid]');
    const instantGrid = root.querySelector('[data-catalogo-instant]');
    const count = root.querySelector('[data-catalogo-count]');
    const chips = root.querySelector('[data-catalogo-chips]');
    const loadMore = root.querySelector('[data-catalogo-more]');
    const live = root.querySelector('[data-catalogo-live]');
    const endpoint = root.dataset.catalogoEndpoint;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const cartBase = root.dataset.catalogoCartBase ?? '/carrito';

    const state = {
        search: '',
        category_id: '',
        brand: '',
        sport_type: '',
        gender: '',
        max_price: '',
        featured: '',
        page: 1,
        lastPage: 1,
        loading: false,
        active: false,
        controller: null,
    };

    const readForm = () => {
        const data = new FormData(form);
        state.search = (data.get('search') ?? '').toString().trim();
        state.category_id = (data.get('category_id') ?? '').toString();
        state.brand = (data.get('brand') ?? '').toString();
        state.sport_type = (data.get('sport_type') ?? '').toString();
        state.gender = (data.get('gender') ?? '').toString();
        state.max_price = (data.get('max_price') ?? '').toString();
        state.featured = form.querySelector('[name="featured"]')?.checked ? '1' : '';
    };

    const params = (page) => {
        const query = new URLSearchParams();
        if (state.search) {
            query.set('search', state.search);
        }
        if (state.category_id) {
            query.set('category_id', state.category_id);
        }
        if (state.brand) {
            query.set('brand', state.brand);
        }
        if (state.sport_type) {
            query.set('sport_type', state.sport_type);
        }
        if (state.gender) {
            query.set('gender', state.gender);
        }
        if (state.max_price) {
            query.set('max_price', state.max_price);
        }
        if (state.featured) {
            query.set('featured', state.featured);
        }
        if (page > 1) {
            query.set('page', String(page));
        }
        return query;
    };

    const syncUrl = () => {
        const url = new URL(window.location.href);
        url.search = params(1).toString();
        window.history.replaceState({}, '', url);
    };

    const renderChips = () => {
        const active = [
            ['search', state.search ? `“${state.search}”` : ''],
            ['category_id', state.category_id ? 'Categoría' : ''],
            ['brand', state.brand],
            ['sport_type', state.sport_type],
            ['gender', state.gender],
            ['max_price', state.max_price ? `≤ $${state.max_price}` : ''],
            ['featured', state.featured ? 'Destacados' : ''],
        ].filter(([, label]) => label);

        if (active.length === 0) {
            chips.innerHTML = '';
            chips.classList.add('hidden');
            return;
        }
        chips.classList.remove('hidden');
        chips.innerHTML = active.map(([key, label]) => `
            <button type="button" data-chip="${esc(key)}"
                    class="focus-volt inline-flex items-center gap-1 rounded-full border border-line bg-white px-3 py-1 text-sm font-medium text-carbon hover:border-carbon">
                ${esc(label)} <span aria-hidden="true">×</span>
            </button>`).join('');
    };

    const skeletons = (n = 8) => Array.from({ length: n }, () => '<div class="skeleton h-72" aria-hidden="true"></div>').join('');

    const emptyState = () => `
        <div class="col-span-full text-center py-12">
            <h3 class="text-heading-md text-gray-600 mb-2">No se encontraron productos</h3>
            <p class="text-body-md text-gray-500">Intenta ajustar tus filtros de búsqueda</p>
        </div>`;

    async function fetchPage(page, { append = false } = {}) {
        if (state.loading) {
            return;
        }
        state.loading = true;
        state.controller?.abort();
        state.controller = new AbortController();

        if (!append) {
            instantGrid.innerHTML = skeletons();
            instantGrid.classList.remove('hidden');
            grid.classList.add('hidden');
            loadMore.classList.add('hidden');
        } else {
            loadMore.disabled = true;
            loadMore.textContent = 'Cargando…';
        }
        live.textContent = 'Buscando productos…';

        try {
            const response = await fetch(`${endpoint}?${params(page).toString()}`, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                signal: state.controller.signal,
            });
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            const payload = await response.json();
            state.lastPage = payload.meta.last_page;
            state.page = payload.meta.current_page;
            state.active = true;

            const html = payload.data.map((product) => cardHtml(product, csrf, cartBase)).join('');
            instantGrid.innerHTML = append ? instantGrid.innerHTML + html : (html || emptyState());
            count.textContent = `${payload.meta.total} productos encontrados · ${payload.meta.elapsed_ms} ms`;
            live.textContent = `${payload.meta.total} productos encontrados.`;
            renderChips();
            syncUrl();

            loadMore.classList.toggle('hidden', state.page >= state.lastPage);
            loadMore.disabled = false;
            loadMore.textContent = 'Cargar más';
        } catch (error) {
            if (error?.name === 'AbortError') {
                return;
            }
            // Ante cualquier fallo, volver al render server-side (fallback seguro).
            instantGrid.classList.add('hidden');
            grid.classList.remove('hidden');
            live.textContent = 'Búsqueda no disponible, mostrando resultados de la página.';
        } finally {
            state.loading = false;
        }
    }

    let debounce = null;
    const schedule = () => {
        window.clearTimeout(debounce);
        debounce = window.setTimeout(() => {
            readForm();
            fetchPage(1);
        }, 250);
    };

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        readForm();
        fetchPage(1);
    });
    form.addEventListener('input', (event) => {
        if (event.target.name === 'search') {
            schedule();
        }
    });
    form.addEventListener('change', (event) => {
        if (event.target.name !== 'search') {
            readForm();
            fetchPage(1);
        }
    });
    chips.addEventListener('click', (event) => {
        const button = event.target.closest('[data-chip]');
        if (!button) {
            return;
        }
        const key = button.dataset.chip;
        const field = form.querySelector(`[name="${key}"]`);
        if (!field) {
            return;
        }
        if (field.type === 'checkbox') {
            field.checked = false;
        } else {
            field.value = '';
        }
        readForm();
        fetchPage(1);
    });
    root.querySelector('[data-catalogo-clear]')?.addEventListener('click', () => {
        form.reset();
        readForm();
        fetchPage(1);
    });
    loadMore.addEventListener('click', () => fetchPage(state.page + 1, { append: true }));

    // Restaurar filtros si la URL ya trae query (compartir/botón atrás).
    const urlParams = new URLSearchParams(window.location.search);
    if ([...urlParams.keys()].length > 0) {
        for (const [key, value] of urlParams) {
            const field = form.querySelector(`[name="${key}"]`);
            if (!field) {
                continue;
            }
            if (field.type === 'checkbox') {
                field.checked = value === '1';
            } else {
                field.value = value;
            }
        }
        readForm();
        fetchPage(1);
    }
}
