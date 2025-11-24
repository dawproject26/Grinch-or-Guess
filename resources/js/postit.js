// resources/js/postit.js

function revelarLetra(letra) {
    if (!letra) return;
    letra = letra.toString().toUpperCase();

    const matches = document.querySelectorAll(`.letra[data-letra="${letra}"]`);

    if (!matches.length) {
        const evt = new CustomEvent('postit:no-match', { detail: { letra } });
        window.dispatchEvent(evt);
        return;
    }

    matches.forEach(el => {
        if (el.classList.contains('revelada')) return;

        // añade clase revelada que activa la animación
        el.classList.add('revelada');

        const evt = new CustomEvent('postit:revealed', { detail: { letra, element: el } });
        window.dispatchEvent(evt);
    });
}

window.revelarLetra = revelarLetra;

export { revelarLetra };