{{- $plants := slice -}}
{{- range where site.RegularPages "Section" "posts" }}{{ $plants = $plants | append (.Resources.Get "plant.svg").RelPermalink }}{{ end -}}
const plants = {{ $plants | jsonify }};
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const drawing = String.raw`
       \ | /
     '-. \|/ .-'
    --==  (@)  ==--
     .-' /|\ '-.
        / | \
          |
       \  |  /
        \ | /
     ~~~~\|/~~~~`;
console.log('%c' + drawing, 'color: #1f5592; font-family: monospace');
console.log('Hello! This herbarium is built with Hugo, Castor and a plant generator: {{ site.Params.repository }}');

for (const button of document.querySelectorAll('.copy')) {
    button.hidden = false;
    button.addEventListener('click', async () => {
        await navigator.clipboard.writeText(button.closest('.code').querySelector('pre').textContent);
        button.textContent = 'copied';
        setTimeout(() => { button.textContent = 'copy'; }, 1500);
    });
}

const missing = document.getElementById('missing');
if (missing) {
    missing.textContent = location.pathname;
}

// The status line shows how far the page is scrolled, as vim does.
const position = document.getElementById('position');

function updatePosition() {
    const end = document.documentElement.scrollHeight - innerHeight;
    position.textContent = scrollY <= 0 ? 'Top' : (scrollY >= end - 1 ? 'Bot' : Math.round(100 * scrollY / end) + '%');
}

addEventListener('scroll', updatePosition, { passive: true });
updatePosition();

// Keyboard shortcuts, listed in layouts/_partials/footer.html.
const pages = { a: '/about/', h: '/', p: '/projects/', t: '/talks/' };
let afterG = false;

function focusEntry(step) {
    const links = [...document.querySelectorAll('.entry a')];
    if (0 === links.length) {
        return;
    }
    const current = links.indexOf(document.activeElement);
    const next = -1 === current ? (step > 0 ? 0 : links.length - 1) : Math.min(links.length - 1, Math.max(0, current + step));
    links[next].focus();
    links[next].scrollIntoView({ block: 'nearest', behavior: reducedMotion ? 'auto' : 'smooth' });
}

function follow(rel) {
    const link = document.querySelector(`link[rel=${rel}]`);
    if (link) {
        location.href = link.href;
    }
}

document.addEventListener('keydown', (event) => {
    if (event.ctrlKey || event.metaKey || event.altKey || event.target.closest('input, textarea, select, [contenteditable]')) {
        return;
    }

    konami(event.key);

    if (afterG) {
        afterG = false;
        if (pages[event.key]) {
            location.href = pages[event.key];
        }

        return;
    }

    switch (event.key) {
        case '?': document.getElementById('keys').showModal(); break;
        case 'g': afterG = true; setTimeout(() => { afterG = false; }, 1500); break;
        case 'j': focusEntry(1); break;
        case 'k': focusEntry(-1); break;
        case 'n': follow('next'); break;
        case 'p': follow('prev'); break;
    }
});

// The Konami code grows another plant in the band.
const code = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'b', 'a'];
let typed = 0;

function konami(key) {
    typed = key === code[typed] ? typed + 1 : (key === code[0] ? 1 : 0);
    if (typed < code.length) {
        return;
    }
    typed = 0;

    const band = document.querySelector('.band');
    const plant = band.querySelector('.plant');
    const grow = () => {
        band.classList.remove('regrow');
        // Reading the width restarts the animation.
        void band.offsetWidth;
        band.classList.add('regrow');
    };

    if (!plant) {
        grow();

        return;
    }

    const others = plants.filter((url) => !plant.src.endsWith(url));
    plant.addEventListener('load', grow, { once: true });
    plant.classList.remove('plant-dried');
    plant.src = others[Math.floor(Math.random() * others.length)];
}
