<?php
/**
 * Paso adicional del formulario multi-step: Propietarios Adicionales
 * Se incluye DENTRO del <form> de nuevaPropiedadFrom.php
 * Los datos se envían como arrays paralelos:
 *   adic_doc[], adic_nombre[], adic_telefono[], adic_email[],
 *   adic_banco[], adic_tipoCuenta[], adic_numeroCuenta[], adic_diaPago[], adic_porcentaje[]
 */

$bancosAdic = [
    'Bancolombia','Davivienda','BBVA','Banco de Bogotá','Banco de Occidente',
    'Colpatria','Banco Popular','Nequi','Movii','PSE','GNB Sudameris','Otro',
];
?>
<!-- ═══════════════════════════════════════════════════════════════
     PASO EXTRA — PROPIETARIOS ADICIONALES
     (El propietario principal ya se registró en los pasos anteriores)
════════════════════════════════════════════════════════════════ -->
<div class="step p-1" id="step-propietarios-adic">
    <h2><i class="bi bi-people-fill text-primary"></i> Propietarios Adicionales</h2>
    <span class="form-label text-indigo-dark">
        Si el inmueble tiene más de un propietario, agrégalos aquí.
        <i class="bi bi-info-circle"></i> El propietario principal ya fue registrado en los pasos anteriores.
    </span>
    <br><br>

    <div id="lista-propietarios-adic">
        <!-- Los bloques se inyectan dinámicamente con JS -->
    </div>

    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="agregarPropietarioAdic()">
        <i class="bi bi-person-plus-fill"></i> Agregar propietario adicional
    </button>

    <div class="alert alert-info mt-3 py-2" style="font-size:0.85rem;">
        <i class="bi bi-lightbulb"></i>
        Si el inmueble tiene un solo propietario, deja esta sección vacía y guarda directamente.
    </div>
</div>

<style>
    .card-propietario-adic {
        border: 1px solid #dee2e6;
        border-left: 4px solid #0d6efd;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 12px;
        background: #f8f9ff;
        position: relative;
    }
    .card-propietario-adic .btn-quitar {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .badge-propietario {
        background: #0d6efd;
        color: #fff;
        font-size: 0.75rem;
        padding: 3px 10px;
        border-radius: 20px;
        margin-bottom: 10px;
        display: inline-block;
    }
</style>

<script>
    let contadorPropAd = 0;

    const bancosOpciones = <?= json_encode($bancosAdic) ?>;

    function bancosSelect(name, idx) {
        let html = `<select name="${name}" class="form-control form-control-sm" required>
            <option value="">Seleccione banco</option>`;
        bancosOpciones.forEach(b => { html += `<option value="${b}">${b}</option>`; });
        html += `</select>`;
        return html;
    }

    function agregarPropietarioAdic() {
        contadorPropAd++;
        const idx = contadorPropAd;

        const diasOpts = Array.from({length: 31}, (_, i) => i + 1)
            .map(d => `<option value="${d}">${d}</option>`).join('');

        const html = `
        <div class="card-propietario-adic" id="prop-adic-${idx}">
            <span class="badge-propietario">Propietario adicional #${idx}</span>
            <button type="button" class="btn btn-sm btn-outline-danger btn-quitar" onclick="quitarPropietarioAdic(${idx})">
                <i class="bi bi-trash3-fill"></i> Quitar
            </button>

            <div class="row g-2 mt-1">
                <div class="col-md-4 col-sm-12">
                    <label class="form-label fw-semibold">Documento (CC / NIT) <span class="text-danger">*</span></label>
                    <input type="text" name="adic_doc[]" class="form-control form-control-sm"
                           placeholder="Ej. 1023456789" required>
                </div>
                <div class="col-md-4 col-sm-12">
                    <label class="form-label fw-semibold">Nombre completo <span class="text-danger">*</span></label>
                    <input type="text" name="adic_nombre[]" class="form-control form-control-sm"
                           placeholder="Nombre del propietario" required>
                </div>
                <div class="col-md-4 col-sm-12">
                    <label class="form-label fw-semibold">% Participación</label>
                    <input type="number" name="adic_porcentaje[]" class="form-control form-control-sm"
                           placeholder="Ej. 50" min="0" max="100" step="0.01" value="0">
                </div>

                <div class="col-md-4 col-sm-12">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input type="tel" name="adic_telefono[]" class="form-control form-control-sm"
                           placeholder="Teléfono de contacto">
                </div>
                <div class="col-md-4 col-sm-12">
                    <label class="form-label fw-semibold">Correo electrónico</label>
                    <input type="email" name="adic_email[]" class="form-control form-control-sm"
                           placeholder="correo@ejemplo.com">
                </div>
                <div class="col-md-4 col-sm-12">
                    <label class="form-label fw-semibold">Día de pago</label>
                    <select name="adic_diaPago[]" class="form-control form-control-sm">
                        <option value="0">Seleccione día</option>
                        ${diasOpts}
                    </select>
                </div>

                <div class="col-md-4 col-sm-12">
                    <label class="form-label fw-semibold">Banco</label>
                    ${bancosSelect('adic_banco[]', idx)}
                </div>
                <div class="col-md-4 col-sm-12">
                    <label class="form-label fw-semibold">Tipo de cuenta</label>
                    <select name="adic_tipoCuenta[]" class="form-control form-control-sm">
                        <option value="">Seleccione tipo</option>
                        <option value="Ahorro">Ahorro</option>
                        <option value="Corriente">Corriente</option>
                    </select>
                </div>
                <div class="col-md-4 col-sm-12">
                    <label class="form-label fw-semibold">Número de cuenta</label>
                    <input type="text" name="adic_numeroCuenta[]" class="form-control form-control-sm"
                           placeholder="Número de cuenta">
                </div>
            </div>
        </div>`;

        document.getElementById('lista-propietarios-adic').insertAdjacentHTML('beforeend', html);
        actualizarResumen();
    }

    function quitarPropietarioAdic(idx) {
        const el = document.getElementById('prop-adic-' + idx);
        if (el) el.remove();
        actualizarResumen();
    }

    function actualizarResumen() {
        const total = document.querySelectorAll('[id^="prop-adic-"]').length;
        const btn   = document.querySelector('[onclick="agregarPropietarioAdic()"]');
        if (total > 0) {
            btn.innerHTML = `<i class="bi bi-person-plus-fill"></i> Agregar otro propietario (${total} registrado${total > 1 ? 's' : ''})`;
        } else {
            btn.innerHTML = `<i class="bi bi-person-plus-fill"></i> Agregar propietario adicional`;
        }
    }
</script>
