import Alpine from 'alpinejs';

window.Alpine = Alpine;

// O texto visível usa separadores brasileiros; a busca envia sempre decimal sem máscara.
function formatMoney(value, complete = false) {
    if (value === '' || value === null || value === undefined) return '';
    const [integer, decimals = ''] = String(value).split('.');
    const grouped = (integer || '0').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return grouped + ((complete || decimals !== '') ? `,${complete ? (decimals + '00').slice(0, 2) : decimals}` : '');
}

function moneyFilterState(filters) {
    return {
        maxPrice: filters.max_price ?? '',
        maxPriceDisplay: formatMoney(filters.max_price, true),
        priceCurrency: filters.price_currency || 'BRL',
        minArea: filters.min_area ?? '',
        areaUnit: filters.area_unit || 'ha',

        onPriceInput(event) {
            const input = event.target;
            const original = input.value;
            const cursor = input.selectionStart ?? original.length;
            const comma = original.indexOf(',');
            const inDecimals = comma >= 0 && cursor > comma;
            const digitsBefore = (inDecimals ? original.slice(comma + 1, cursor) : original.slice(0, cursor))
                .replace(/\D/g, '').length;
            const parts = original.replace(/[^\d,]/g, '').split(',');
            const integer = (parts[0] || '0').replace(/^0+(?=\d)/, '');
            const decimals = parts.slice(1).join('').slice(0, 2);
            const hasComma = original.includes(',');

            this.maxPrice = integer === '0' && !original.match(/\d/) ? ''
                : `${integer}${decimals ? `.${decimals}` : ''}`;
            this.maxPriceDisplay = this.maxPrice === '' ? ''
                : formatMoney(this.maxPrice) + (hasComma && !decimals ? ',' : '');
            input.value = this.maxPriceDisplay;

            let position = 0;
            if (inDecimals) {
                position = this.maxPriceDisplay.indexOf(',') + 1 + digitsBefore;
            } else {
                let seen = 0;
                while (position < this.maxPriceDisplay.length && seen < digitsBefore) {
                    if (/\d/.test(this.maxPriceDisplay[position])) seen++;
                    position++;
                }
            }
            input.setSelectionRange(position, position);
        },

        onPriceBlur(event) {
            this.maxPriceDisplay = formatMoney(this.maxPrice, true);
            event.target.value = this.maxPriceDisplay;
        },

        normalizePriceOnSubmit(event) {
            const input = event.target.elements.namedItem('max_price');
            if (input) input.value = this.maxPrice;
        },
    };
}

// Estado do filtro da home. As regiões acompanham a categoria selecionada e
// uma região deixa de ser enviada se não existir na nova categoria.
Alpine.data('listingFilter', (regionsByCategory, filters) => ({
    category: filters.category || 'fazenda',
    q: filters.q || '',
    region: filters.region || '',
    ...moneyFilterState(filters),
    regionsByCategory,

    get regions() {
        return this.regionsByCategory[this.category] || [];
    },

    syncRegion() {
        if (this.region && !this.regions.includes(this.region)) this.region = '';
    },
}));

// Busca ao vivo dos classificados por categoria. Atualiza resultados e URL
// sem recarregar; trocar de categoria navega para o catálogo correspondente.
Alpine.data('categorySearch', (baseUrl, filters, categoryRoutes, regionsByCategory) => ({
    category: filters.category || 'fazenda',
    q: filters.q || '',
    region: filters.region || '',
    ...moneyFilterState(filters),
    categoryRoutes,
    regionsByCategory,
    loading: false,
    requestController: null,

    get regions() {
        return this.regionsByCategory[this.category] || [];
    },

    syncRegion() {
        if (this.region && !this.regions.includes(this.region)) this.region = '';
    },

    params() {
        const params = new URLSearchParams();
        if (this.q.trim()) params.set('q', this.q.trim());
        if (this.region) params.set('region', this.region);
        if (this.maxPrice !== '' && this.maxPrice !== null) {
            params.set('max_price', this.maxPrice);
            params.set('price_currency', this.priceCurrency);
        }
        if (this.minArea !== '' && this.minArea !== null) {
            params.set('min_area', this.minArea);
            params.set('area_unit', this.areaUnit);
        }

        return params;
    },

    changeCategory() {
        this.syncRegion();
        const params = this.params();
        const target = this.categoryRoutes[this.category];
        window.location.assign(params.toString() ? `${target}?${params.toString()}` : target);
    },

    async search() {
        this.syncRegion();
        this.loading = true;
        this.requestController?.abort();
        const controller = new AbortController();
        this.requestController = controller;

        const params = this.params();
        const url = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;

        try {
            const response = await fetch(url, {
                headers: { 'X-PIB-Partial': 'true' },
                signal: controller.signal,
            });
            if (!response.ok) throw new Error(`Search failed with status ${response.status}`);
            const html = await response.text();
            document.getElementById('category-results').innerHTML = html;
            window.history.pushState({}, '', url);
        } catch (error) {
            if (error.name !== 'AbortError') console.error(error);
        } finally {
            // Uma resposta abortada não pode encerrar o indicador da busca seguinte.
            if (this.requestController === controller) {
                this.requestController = null;
                this.loading = false;
            }
        }
    },
}));

// Carrossel "coverflow" 3D (autoplay em loop + arraste/clique) usado nos
// cards de eventos (Início e Informações). Card ativo à frente/maior; os
// vizinhos ficam menores, rotacionados e com opacidade reduzida, girando em
// loop contínuo até o visitante interagir.
Alpine.data('coverflow', (count) => ({
    active: 0,
    count,
    paused: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
    dragging: false,
    dragStartX: 0,
    justDragged: false,
    autoplayTimer: null,
    resumeTimer: null,

    init() {
        this.startAutoplay();
    },

    startAutoplay() {
        if (this.count <= 1) return;
        this.autoplayTimer = setInterval(() => {
            if (!this.paused) this.next();
        }, 3800);
    },

    /** Pausa a rotação automática e agenda a retomada após inatividade. */
    scheduleResume() {
        this.paused = true;
        clearTimeout(this.resumeTimer);
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        this.resumeTimer = setTimeout(() => { this.paused = false; }, 4500);
    },

    next() {
        this.active = (this.active + 1) % this.count;
    },

    prev() {
        this.active = (this.active - 1 + this.count) % this.count;
    },

    /** Distância circular (com sinal) do item até o card ativo, para o loop. */
    distance(index) {
        let d = index - this.active;
        if (d > this.count / 2) d -= this.count;
        if (d < -this.count / 2) d += this.count;
        return d;
    },

    cardStyle(index) {
        const d = this.distance(index);
        const abs = Math.abs(d);

        if (abs > 2) {
            return 'opacity: 0; pointer-events: none; transform: translateX(-50%) scale(0.5);';
        }

        const offset = d * 130;
        const scale = 1 - abs * 0.16;
        const rotate = d * -22;
        const opacity = 1 - abs * 0.38;
        const z = 10 - abs;

        return `transform: translateX(calc(-50% + ${offset}px)) scale(${scale}) rotateY(${rotate}deg); `
            + `opacity: ${opacity}; z-index: ${z};`;
    },

    onCardClick(index, openModal) {
        if (this.justDragged) {
            this.justDragged = false;
            return;
        }
        openModal();
        this.scheduleResume();
    },

    dragStart(e) {
        this.dragging = true;
        this.dragStartX = e.clientX;
        this.paused = true;
        // Mantém o gesto mesmo se o ponteiro sair da área do card antes de soltar.
        e.target.setPointerCapture?.(e.pointerId);
    },

    dragMove(e) {
        // Sem preview em tempo real (mantém simples e robusto); a decisão
        // de navegar acontece em dragEnd, com base na distância total.
        if (!this.dragging) return;
        e.preventDefault();
    },

    dragEnd(e) {
        if (!this.dragging) return;
        this.dragging = false;

        const delta = e.clientX - this.dragStartX;
        if (Math.abs(delta) > 40) {
            this.justDragged = true;
            delta < 0 ? this.next() : this.prev();
        }
        this.scheduleResume();
    },
}));

Alpine.start();

// Revelação suave de blocos marcados com [data-reveal] ao entrarem na tela.
// Storytelling de entrada do hero/seções; não roda se o usuário pediu menos movimento.
if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && 'IntersectionObserver' in window) {
    const revealElements = document.querySelectorAll('[data-reveal]');

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.15 },
    );

    revealElements.forEach((el) => {
        el.classList.add('reveal-pending');
        observer.observe(el);
    });
} else {
    document.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('is-visible'));
}

// Vídeo de fundo do hero.
//
// O markup sai do servidor SEM `autoplay` de propósito: quem decide dar play é
// este bloco. Isso resolve duas coisas de uma vez —
//   1. `prefers-reduced-motion: reduce` nunca chega a ver movimento algum;
//   2. sem mídia cadastrada o servidor renderiza apenas o poster estático.
// Também pausamos quando o hero sai de vista, para não gastar bateria à toa.
{
    const heroVideos = document.querySelectorAll('video[data-hero-video]');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    if (heroVideos.length) {
        const sync = () => {
            heroVideos.forEach((video) => {
                if (reduceMotion.matches) {
                    video.pause();
                    video.currentTime = 0;
                } else {
                    // Rejeita quando o arquivo não existe ou o navegador bloqueia: o poster fica.
                    video.play().catch(() => {});
                }
            });
        };

        sync();
        reduceMotion.addEventListener('change', sync);

        if ('IntersectionObserver' in window) {
            const visibility = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (reduceMotion.matches) return;

                        if (entry.isIntersecting) {
                            entry.target.play().catch(() => {});
                        } else {
                            entry.target.pause();
                        }
                    });
                },
                { threshold: 0.1 },
            );

            heroVideos.forEach((video) => visibility.observe(video));
        }
    }
}
