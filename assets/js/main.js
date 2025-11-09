document.addEventListener('DOMContentLoaded', () => {
    const galleries = document.querySelectorAll('[data-gallery]');

    galleries.forEach(gallery => {
        const track = gallery.querySelector('.gallery-track');
        const dots = gallery.querySelectorAll('.gallery-dot');

        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                const index = parseInt(dot.getAttribute('data-slide'), 10);
                const offset = index * -100;
                track.style.transform = `translateX(${offset}%)`;

                dots.forEach(d => d.classList.remove('is-active'));
                dot.classList.add('is-active');
            });
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    initGalleryCarousel();
    initLightboxGalerie(); // tu l’avais déjà
});

function initGalleryCarousel() {
    const carousels = document.querySelectorAll('[data-gallery-carousel]');
    if (!carousels.length) return;

    carousels.forEach((carousel) => {
        const track  = carousel.querySelector('.gallery-track');
        const slides = Array.from(carousel.querySelectorAll('.gallery-item'));
        const dots   = Array.from(carousel.querySelectorAll('.gallery-dot'));
        const prev   = carousel.querySelector('.gallery-arrow--prev');
        const next   = carousel.querySelector('.gallery-arrow--next');

        if (!track || !slides.length) return;

        function getItemsPerView() {
            // 4 images visibles sur tablette / desktop, 1 sur mobile
            return window.innerWidth >= 768 ? 4 : 1;
        }

        let itemsPerView = getItemsPerView();
        let currentIndex = 0;

        function goTo(index) {
            const total = slides.length;
            if (!total) return;

            itemsPerView = getItemsPerView();

            // index = "index de la première image visible"
            const maxIndex = Math.max(0, total - itemsPerView);

            if (index < 0) {
                currentIndex = maxIndex; // boucle vers la fin
            } else if (index > maxIndex) {
                currentIndex = 0;        // boucle vers le début
            } else {
                currentIndex = index;
            }

            // Décalage en fonction du nombre d’items visibles
            const stepPercent = 100 / itemsPerView;
            const offset = -currentIndex * stepPercent;

            track.style.transform = `translateX(${offset}%)`;

            // Active / désactive les classes sur les slides
            slides.forEach((slide, i) => {
                const inView =
                    i >= currentIndex &&
                    i < currentIndex + itemsPerView;
                slide.classList.toggle('is-active', inView);
            });

            // Mise à jour des dots (basée sur "page" = index d’itemsPerView)
            dots.forEach((dot, i) => {
                const dotIndex = i;
                const page = Math.floor(currentIndex / itemsPerView);
                dot.classList.toggle('is-active', dotIndex === page);
            });
        }

        // Navigation flèches : on décale d’1 image (plus smooth)
        if (next) {
            next.addEventListener('click', function () {
                goTo(currentIndex + 1);
            });
        }

        if (prev) {
            prev.addEventListener('click', function () {
                goTo(currentIndex - 1);
            });
        }

        // Navigation par dots : chaque dot correspond à une "page"
        dots.forEach((dot, i) => {
            dot.addEventListener('click', function () {
                itemsPerView = getItemsPerView();
                goTo(i * itemsPerView);
            });
        });

        // Réajuster sur resize
        window.addEventListener('resize', function () {
            goTo(currentIndex);
        });

        // Ouverture de la lightbox au clic sur une image (inchangé)
        const lightbox    = document.querySelector('#galerie-lightbox');
        const lightboxImg = lightbox ? lightbox.querySelector('.lightbox__img') : null;

        slides.forEach((slide) => {
            const img = slide.querySelector('img');
            if (!img) return;

            img.addEventListener('click', function () {
                if (!lightbox || !lightboxImg) return;
                lightboxImg.src = img.dataset.full || img.src;
                lightbox.classList.add('is-open');
                lightbox.setAttribute('aria-hidden', 'false');
            });
        });

        // Position initiale
        goTo(0);
    });
}

function initLightboxGalerie() {
    const lightbox = document.querySelector('#galerie-lightbox');
    if (!lightbox) return;

    const closeBtn = lightbox.querySelector('.lightbox__close');
    const backdrop = lightbox.querySelector('.lightbox__backdrop');

    function closeLightbox() {
        lightbox.classList.remove('is-open');
        lightbox.setAttribute('aria-hidden', 'true');
        const img = lightbox.querySelector('.lightbox__img');
        if (img) {
            img.src = '';
        }
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeLightbox);
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeLightbox);
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && lightbox.classList.contains('is-open')) {
            closeLightbox();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('.main-nav');

    if (toggle && nav) {
        toggle.addEventListener('click', () => {
            nav.classList.toggle('open');
            toggle.classList.toggle('open');
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const navLinks = Array.from(
        document.querySelectorAll('.main-nav .menu a')
    );

    if (!navLinks.length) return;

    const sections = navLinks
        .map(link => {
            let href = link.getAttribute('href');
            let id = null;

            try {
                const url = new URL(href, window.location.origin);
                id = url.hash !== '' ? url.hash.replace('#', '') : 'top';
            } catch (e) {
                if (href.includes('#')) {
                    id = href.split('#')[1];
                }else{
                    id = 'top';
                }
            }

            const section = id ? document.getElementById(id) : null;
            return section ? { link, section } : null;
        })
        .filter(Boolean);

    if (!sections.length) return;

    const setActiveLinkForId = (id) => {
        navLinks.forEach(link => {
            let href = link.getAttribute('href');
            const hash = href.includes('#') ? href.split('#')[1] : ( href.includes('prise-de-rendez-vous') ? '' :'top' );
            link.classList.toggle('is-active', hash === id);
        });
    };

    const observer = new IntersectionObserver(
        (entries) => {
            const visible = entries
                .filter(entry => entry.isIntersecting)
                .sort((a, b) => a.target.offsetTop - b.target.offsetTop);

            if (!visible.length) return;

            const currentSection = visible[0].target;
            setActiveLinkForId(currentSection.id);
        },
        { threshold: 0.5 }
    );

    sections.forEach(({ section }) => observer.observe(section));

    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            let href = link.getAttribute('href');
            let id = href.includes('#') ? href.split('#')[1] : 'top';
            setActiveLinkForId(id);
        });
    });
});

