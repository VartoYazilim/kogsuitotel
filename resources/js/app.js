import flatpickr from 'flatpickr';
import { Turkish } from 'flatpickr/dist/l10n/tr.js';
import 'flatpickr/dist/flatpickr.min.css';

flatpickr.localize(Turkish);

/**
 * Bir odanın müsait olmayan tarih aralıklarını API'den çeker.
 * Cache'lidir — aynı slug için ikinci çağrıda tekrar fetch yok.
 */
const unavailableCache = new Map();

async function fetchUnavailableDates(slug) {
    if (!slug) return [];
    if (unavailableCache.has(slug)) return unavailableCache.get(slug);

    try {
        const res = await fetch(`/api/rooms/${slug}/unavailable-dates`, {
            headers: { Accept: 'application/json' },
        });
        if (!res.ok) return [];
        const data = await res.json();
        unavailableCache.set(slug, data);
        return data;
    } catch (e) {
        console.warn('Müsait olmayan tarihler getirilemedi:', e);
        return [];
    }
}

/**
 * Rezervasyon formundaki giriş+çıkış picker'larını eşler.
 * Oda seçimi değiştiğinde dolu günleri günceller.
 */
function initReservationDatePickers() {
    const checkInEl = document.querySelector('[data-fp-checkin]');
    const checkOutEl = document.querySelector('[data-fp-checkout]');
    const roomSelectEl = document.querySelector('[data-fp-room-select]');

    if (!checkInEl || !checkOutEl) return;

    const commonOpts = {
        minDate: 'today',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd F Y, l',
        disableMobile: true, // Flatpickr UI mobile'da da kullansin; native fallback eski format gosteriyordu
        animate: true,
    };

    const checkInPicker = flatpickr(checkInEl, {
        ...commonOpts,
        onChange: ([date]) => {
            if (!date) return;
            const next = new Date(date);
            next.setDate(next.getDate() + 1);
            checkOutPicker.set('minDate', next);
            // Çıkış zaten dolduysa, giriş'ten önce ise temizle
            if (checkOutPicker.selectedDates[0] && checkOutPicker.selectedDates[0] <= date) {
                checkOutPicker.clear();
            }
            window.__recalcReservationSummary?.();
        },
    });

    const checkOutPicker = flatpickr(checkOutEl, {
        ...commonOpts,
        onChange: () => window.__recalcReservationSummary?.(),
    });

    async function applyUnavailableDates(slug) {
        const dates = await fetchUnavailableDates(slug);
        checkInPicker.set('disable', dates);
        checkOutPicker.set('disable', dates);
    }

    // İlk yüklemede eğer oda seçili gelmişse (URL ?oda=X), dolu günleri çek
    if (roomSelectEl) {
        const initialOption = roomSelectEl.options[roomSelectEl.selectedIndex];
        const initialSlug = initialOption?.dataset?.slug;
        if (initialSlug) {
            applyUnavailableDates(initialSlug);
        }

        roomSelectEl.addEventListener('change', (e) => {
            const opt = e.target.options[e.target.selectedIndex];
            const slug = opt?.dataset?.slug;
            if (slug) {
                applyUnavailableDates(slug);
            }
        });
    }
}

/**
 * Hero/header/oda detay gibi basit form'larda (room slug DOM'da fixed) tek tip picker.
 * Sadece minDate today, TR locale, "Tarih Seçiniz" placeholder.
 */
function initSimpleDatePickers() {
    document.querySelectorAll('[data-fp-simple]').forEach((el) => {
        const linkedTo = el.dataset.fpLinkedTo;
        const roomSlug = el.dataset.fpRoomSlug;

        const opts = {
            minDate: 'today',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd F Y, l',
            disableMobile: true, // Flatpickr UI mobile'da da kullansin; native fallback eski format gosteriyordu
        };

        const picker = flatpickr(el, opts);

        // Oda slug verilmişse dolu günleri uygula
        if (roomSlug) {
            fetchUnavailableDates(roomSlug).then((dates) => picker.set('disable', dates));
        }

        // Eşli picker: bu giriş ise, çıkışın minDate'i otomatik güncellensin
        if (linkedTo) {
            const target = document.querySelector(linkedTo);
            if (target && target._flatpickr) {
                picker.config.onChange.push(([date]) => {
                    if (!date) return;
                    const next = new Date(date);
                    next.setDate(next.getDate() + 1);
                    target._flatpickr.set('minDate', next);
                });
            }
        }
    });
}

/**
 * Rezervasyon özetinde oda + tarih + gece hesabını canlı günceller.
 * Sticky sidebar'daki "Özet" panelinin dinamik kısımları.
 */
function initReservationSummary() {
    const roomSelect = document.querySelector('[data-fp-room-select]');
    const checkInEl = document.querySelector('[data-fp-checkin]');
    const checkOutEl = document.querySelector('[data-fp-checkout]');
    const elRoom = document.querySelector('[data-summary-room]');
    const elNights = document.querySelector('[data-summary-nights]');
    const elPricePerNight = document.querySelector('[data-summary-price-per-night]');
    const elTotal = document.querySelector('[data-summary-total]');

    if (!roomSelect || !elTotal) return;

    const formatter = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });
    const formatTry = (n) => '₺' + formatter.format(n);

    function recalc() {
        const opt = roomSelect.options[roomSelect.selectedIndex];
        const price = parseFloat(opt?.dataset?.price ?? '0');
        const roomName = opt?.dataset?.name ?? null;

        let nights = 0;
        if (checkInEl?.value && checkOutEl?.value) {
            const a = new Date(checkInEl.value);
            const b = new Date(checkOutEl.value);
            const diff = Math.round((b - a) / 86400000);
            nights = diff > 0 ? diff : 0;
        }

        elRoom.textContent = roomName || '— Seçilmedi —';
        elNights.textContent = nights > 0 ? `${nights} gece` : '—';
        elPricePerNight.textContent = price > 0 ? `${formatTry(price)} / gece` : '—';
        elTotal.textContent = price > 0 && nights > 0 ? formatTry(price * nights) : '₺—';
    }

    roomSelect.addEventListener('change', recalc);
    // Flatpickr 'change' event'i de yayar — input change'iyle yakalanır
    checkInEl?.addEventListener('change', recalc);
    checkOutEl?.addEventListener('change', recalc);

    // Global olarak da çağrılabilsin (Flatpickr onChange'ten)
    window.__recalcReservationSummary = recalc;

    // İlk yükleme
    recalc();
}

/**
 * Hero formundaki oda secimi degistiginde, secilen odanin dolu gunleri
 * giris/cikis tarihi Flatpickr'larina disable olarak inject edilir.
 * Boylece kullanici "once oda sec sonra tarih" akisinda dogru sinyalleri alir.
 */
function initHeroForm() {
    const roomSelect = document.querySelector('[data-fp-hero-room]');
    const checkInEl = document.querySelector('#hero-checkin');
    const checkOutEl = document.querySelector('#hero-checkout');

    if (!roomSelect || !checkInEl || !checkOutEl) return;

    async function applyDates(slug) {
        const dates = slug ? await fetchUnavailableDates(slug) : [];
        checkInEl._flatpickr?.set('disable', dates);
        checkOutEl._flatpickr?.set('disable', dates);
    }

    roomSelect.addEventListener('change', (e) => applyDates(e.target.value));

    // URL'den prefill ile gelen oda varsa ilk yuklemede uygula
    if (roomSelect.value) applyDates(roomSelect.value);
}

/**
 * Olive Sanctuary lightbox — vanilla. Tüm `[data-lightbox-trigger]` butonları
 * gruplandırılır (data-lightbox-group), modal'da prev/next + Esc + Arrow keys.
 * Body scroll lock + focus trap minimal (Esc/close button focusable).
 */
function initLightbox() {
    const modal = document.getElementById('kog-lightbox');
    if (!modal) return;

    const img = modal.querySelector('[data-lightbox-image]');
    const caption = modal.querySelector('[data-lightbox-caption]');
    const counter = modal.querySelector('[data-lightbox-counter]');
    const prevBtn = modal.querySelector('[data-lightbox-prev]');
    const nextBtn = modal.querySelector('[data-lightbox-next]');
    const closeBtn = modal.querySelector('[data-lightbox-close]');

    let currentGroup = [];
    let currentIndex = 0;
    let triggerEl = null; // Modal kapanınca focus'u geri vermek için

    function render() {
        const item = currentGroup[currentIndex];
        if (!item) return;
        img.src = item.src;
        img.alt = item.alt;
        caption.textContent = item.alt;
        counter.textContent = currentGroup.length > 1
            ? `${currentIndex + 1} / ${currentGroup.length}`
            : '';

        const multi = currentGroup.length > 1;
        prevBtn.classList.toggle('hidden', !multi);
        nextBtn.classList.toggle('hidden', !multi);
    }

    function open(triggers, startIndex, fromEl) {
        currentGroup = triggers.map((el) => ({
            src: el.dataset.lightboxSrc,
            alt: el.dataset.lightboxAlt || '',
        }));
        currentIndex = startIndex;
        triggerEl = fromEl;
        render();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        closeBtn.focus();
    }

    function close() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        img.src = '';
        triggerEl?.focus();
    }

    function step(delta) {
        if (currentGroup.length < 2) return;
        currentIndex = (currentIndex + delta + currentGroup.length) % currentGroup.length;
        render();
    }

    document.querySelectorAll('[data-lightbox-trigger]').forEach((el) => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            const group = el.dataset.lightboxGroup;
            const triggers = Array.from(
                document.querySelectorAll(`[data-lightbox-trigger][data-lightbox-group="${group}"]`)
            ).sort((a, b) =>
                parseInt(a.dataset.lightboxIndex ?? '0', 10) - parseInt(b.dataset.lightboxIndex ?? '0', 10)
            );
            const startIndex = triggers.indexOf(el);
            open(triggers, startIndex >= 0 ? startIndex : 0, el);
        });
    });

    closeBtn.addEventListener('click', close);
    prevBtn.addEventListener('click', () => step(-1));
    nextBtn.addEventListener('click', () => step(1));

    // Modal'a (image dışı backdrop) tıklayınca kapat
    modal.addEventListener('click', (e) => {
        if (e.target === modal) close();
    });

    document.addEventListener('keydown', (e) => {
        if (modal.classList.contains('hidden')) return;
        if (e.key === 'Escape') close();
        if (e.key === 'ArrowLeft') step(-1);
        if (e.key === 'ArrowRight') step(1);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initReservationDatePickers();
    initSimpleDatePickers();
    initHeroForm();
    initReservationSummary();
    initLightbox();
});
