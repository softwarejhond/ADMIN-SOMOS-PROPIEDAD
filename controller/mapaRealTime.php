 <?php
    // Consulta para obtener latitud, longitud y otros datos de las propiedades
    $sqlUbicaciones = "SELECT latitud, longitud, tipoInmueble, direccion, valor_canon FROM proprieter";
    $resultado = $conn->query($sqlUbicaciones);

    $locations = [];
    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $locations[] = [
                'lat' => (float)$row['latitud'],
                'lng' => (float)$row['longitud'],
                'tipoInmueble' => $row['tipoInmueble'],
                'direccion' => $row['direccion'],
                'valorCanon' => $row['valor_canon']
            ];
        }
    }
    ?>

 <style>
     /* Establecer la altura del mapa */
     #map {
         width: 100%;
         height: 500px;
         /* Ajusta la altura según sea necesario */
     }
 </style>
 <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA98OpvjlfBwdRXdIVsGCyNM2ak5o-WYYs&libraries=places&callback=initMap" async defer></script>

 <div id="map"></div>

 <script>
     const locations = <?php echo json_encode($locations); ?>;
     console.log("Ubicaciones obtenidas:", locations);

     function initMap() {
         if (locations.length === 0) {
             alert("No se encontraron ubicaciones.");
             return;
         }

         // Crear el mapa centrado en la primera ubicación
         const map = new google.maps.Map(document.getElementById("map"), {
             zoom: 12,
             center: {
                 lat: locations[0].lat,
                 lng: locations[0].lng
             }, // Centrar en la primera ubicación
         });

         const bounds = new google.maps.LatLngBounds();

         locations.forEach(location => {
             const marker = new google.maps.Marker({
                 position: {
                     lat: location.lat,
                     lng: location.lng
                 }, // Coordenadas correctas
                 map: map,
                 title: location.tipoInmueble || 'Inmueble' // Título del marcador, fallback en caso de que no exista
             });

             // Crear una ventana de información
             const infoWindow = new google.maps.InfoWindow({
                 content: `
                    <div>
                        <h3>${location.tipoInmueble || 'Inmueble'}</h3>
                        <p><strong>Dirección:</strong> ${location.direccion || 'No disponible'}</p>
                        <p><strong>Precio:</strong> $${location.valorCanon || 'No disponible'}</p>
                    </div>
                `
             });

             // Asociar el evento de clic en el marcador
             marker.addListener("click", function() {
                 infoWindow.open(map, marker);
             });

             bounds.extend(marker.getPosition()); // Expandir los límites del mapa para ajustarlo
         });

         // Ajustar el mapa para que se vea todo
         map.fitBounds(bounds);
     }

     // Asegurarse de que el mapa se cargue cuando el DOM esté listo
     window.onload = initMap;
 </script>