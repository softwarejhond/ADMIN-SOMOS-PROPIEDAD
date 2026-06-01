<?php
require('conexion.php');

$sql    = "SELECT direccion, ciudad FROM contratos_somos_propiedad ORDER BY no_contrato DESC LIMIT 40";
$result = $conn->query($sql);

$addresses = [];
$dbError   = '';
if (!$result) {
    $dbError = $conn->error;
} elseif ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $addresses[] = [
            'q'     => htmlspecialchars($row['direccion'] . ', ' . $row['ciudad'] . ', Antioquia, Colombia', ENT_QUOTES, 'UTF-8'),
            'label' => htmlspecialchars($row['direccion'] . ' — ' . $row['ciudad'], ENT_QUOTES, 'UTF-8'),
        ];
    }
}
$addressesJson = json_encode($addresses, JSON_UNESCAPED_UNICODE);
?>

<?php if ($dbError): ?>
<div class="alert alert-danger">Error BD: <?php echo htmlspecialchars($dbError); ?></div>
<?php endif; ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<div class="card shadow-sm h-100" style="overflow:hidden;">
    <div class="card-header fw-bold text-white" style="background-color:#3d2b8e;">
        <i class="bi bi-geo-alt-fill"></i> Ubicaciones de Contratos &mdash; Valle de Aburrá
    </div>
    <div id="mapa-contratos" style="height:480px; width:100%;"></div>
    <div class="card-footer bg-light d-flex align-items-center gap-2 py-2">
        <span class="badge bg-success rounded-pill" id="mapa-contador">0</span>
        <small class="text-muted" id="mapa-estado">Cargando ubicaciones&hellip;</small>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/WLs=" crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var mapaDiv = document.getElementById('mapa-contratos');
    if (!mapaDiv) { return; }

    var map = L.map('mapa-contratos').setView([6.2518, -75.5636], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
    }).addTo(map);

    // Recalcular dimensiones tras el renderizado del layout incluido
    setTimeout(function () { map.invalidateSize(); }, 300);

    var addresses  = <?php echo $addressesJson; ?>;
    var estadoEl   = document.getElementById('mapa-estado');
    var contadorEl = document.getElementById('mapa-contador');
    var found      = 0;

    var markerIcon = L.divIcon({
        className: '',
        html: '<div style="background:#6f42c1;width:14px;height:14px;border-radius:50%;border:2px solid #fff;box-shadow:0 0 6px rgba(0,0,0,.5);"></div>',
        iconSize:   [14, 14],
        iconAnchor: [7, 7]
    });

    function geocodeNext(index) {
        if (index >= addresses.length) {
            estadoEl.textContent = found > 0
                ? found + ' ubicación(es) marcada(s) en el mapa.'
                : 'No se pudieron geolocalizar las direcciones.';
            return;
        }

        var item = addresses[index];
        estadoEl.textContent = 'Buscando: ' + item.label;

        var url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' +
                  encodeURIComponent(item.q);

        fetch(url, { headers: { 'Accept-Language': 'es' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data && data.length > 0) {
                    var lat = parseFloat(data[0].lat);
                    var lon = parseFloat(data[0].lon);
                    L.marker([lat, lon], { icon: markerIcon })
                        .addTo(map)
                        .bindPopup('<b>' + item.label + '</b>');
                    found++;
                    contadorEl.textContent = found;
                }
            })
            .catch(function () {})
            .finally(function () {
                setTimeout(function () { geocodeNext(index + 1); }, 1100);
            });
    }

    if (addresses.length > 0) {
        setTimeout(function () { geocodeNext(0); }, 400);
    } else {
        estadoEl.textContent = 'Sin contratos para mostrar.';
    }
});
</script>