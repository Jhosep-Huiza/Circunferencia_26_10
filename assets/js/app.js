function calcularCircunferencia() {
    const radioInput = document.getElementById('txtRadio');
    const d = document.getElementById('txtD');
    const a = document.getElementById('txtA');
    const p = document.getElementById('txtP');
    if (!radioInput || !d || !a || !p) return false;

    const r = parseFloat(radioInput.value.replace(',', '.'));
    if (isNaN(r) || r <= 0) {
        d.value = a.value = p.value = '';
        dibujarCirculo(null);
        return false;
    }

    d.value = (2 * r).toFixed(4);
    a.value = (Math.PI * r * r).toFixed(4);
    p.value = (2 * Math.PI * r).toFixed(4);
    dibujarCirculo(r);
    return true;
}

function dibujarCirculo(r) {
    const c = document.getElementById('svgCirculo');
    const l = document.getElementById('svgRadio');
    const t = document.getElementById('svgEtiqueta');
    if (!c) return;

    // Tamaño visual aproximado: crece con el radio sin salirse del cuadro
    const px = r ? 22 + 88 * (r / (r + 10)) : 70;
    c.setAttribute('r', px);
    l.setAttribute('x2', 120 + px);
    t.setAttribute('x', 120 + px / 2 - 4);
    t.textContent = r ? 'r = ' + r : 'r';
}

document.addEventListener('DOMContentLoaded', function () {
    const radio = document.getElementById('txtRadio');
    const form = document.getElementById('frmFormulario');
    if (!radio) return;

    radio.addEventListener('input', calcularCircunferencia);
    if (radio.value !== '') calcularCircunferencia();

    // El servidor recalcula siempre; aquí solo evitamos enviar un radio inválido
    if (form) {
        form.addEventListener('submit', function (e) {
            if (!calcularCircunferencia()) {
                e.preventDefault();
                alert('Ingresa un radio mayor a cero');
                radio.focus();
            }
        });
    }
});