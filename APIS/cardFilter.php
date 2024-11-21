<?php
require('conexion.php');

// Definir un valor predeterminado para la variable $buscar
$buscar = isset($_GET['buscar']) ? $conn->real_escape_string($_GET['buscar']) : '';

// Definir el número de ítems por página
define('NUM_ITEMS_BY_PAGE', 2);

// Consulta para contar el total de productos
$sql_count = "SELECT COUNT(*) as total_products 
FROM proprieter
INNER JOIN municipios ON proprieter.Municipio = municipios.id_municipio
WHERE codigo LIKE '%$buscar%' AND nombre_inquilino = '' AND estadoPropietario = 'ACTIVO'";

// Ejecutar la consulta
$result_count = $conn->query($sql_count);

if ($result_count) {
    $row_count = $result_count->fetch_assoc();
    $num_total_rows = $row_count['total_products'];
} else {
    // Manejar el error si la consulta falla
    echo "Error al realizar la consulta: " . $conn->error;
    $num_total_rows = 0;
}

// Formatear un valor ejemplo (reemplazar $row['valor_canon'] con un dato válido)
$row = ['valor_canon' => 1500000]; // Esto es solo un ejemplo para evitar errores
$canonFormateado = number_format($row['valor_canon'], 0, ',', '.');

?>

<!-- Formulario de filtros -->
<form id="filterForm">
    <div class="mt-3">
        <div class="card rounded-bottom ">
            <div class=" card-header boton bg-indigo-dark text-white ">
                <i class=" fa-solid fa-filter"></i> REALIZAR CONSULTA PERSONALIZADA
            </div>
            <br>
            <div class="card-body">
                <div class="row  d-flex justify-content-center">
                    <div class="form-group col-sm-12 col-md-6 col-lg-3 text-left">

                        <label for="tipoInmueble"><i class="bi bi-filter-square-fill"></i> Tipo </label>
                        <select class="form-control" id="tipoInmueble" name="tipoInmueble">
                            <option value="0">Seleccionar</option>
                            <option value="Casa">Casa</option>
                            <option value="Apartamento">Apartamento</option>
                            <option value="Local">Local</option>
                            <option value="Apartaestudio">Apartaestudio</option>
                            <option value="Penthouse">Penthouse</option>
                            <option value="Finca">Finca</option>
                            <option value="Casa con local">Casa con local</option>
                            <option value="LOTE">Lote</option>
                        </select>
                        <label for="estado"><i class="bi bi-filter-square-fill"></i> Estado</label>
                        <select class="form-control" id="estado" name="estado">
                            <option value="">Seleccionar</option>
                            <option value="EN ALQUILER">EN ALQUILER</option>
                            <option value="EN VENTA">EN VENTA</option>
                            <option value="ALQUILER O VENTA">ALQUILER O VENTA</option>
                        </select>

                    </div>



                    <div class="form-group col-sm-12 col-md-6 col-lg-3 text-left">
                        <label for="piso"><i class="fa-solid fa-vihara"></i> Nivel</label>
                        <select id="piso" class="form-control text-center filters">
                            <option value="">Seleccionar</option>
                            <option value="">0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                        </select>
                        <label for="habitaciones"><i class="fa-solid fa-bed"></i> Habitaciones</label>
                        <select id="habitaciones" name="habitaciones" class="form-control text-center filters">
                            <option value="">Seleccionar</option>
                            <option value="">0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                        </select>
                    </div>

                    <div class="form-group col-sm-12 col-md-6 col-lg-3 text-left ">
                        <label for="lista_departamento"><i class="fa-solid fa-map-location-dot"></i> Departamento</label>
                        <select id="lista_departamento" name="departamento" class="form-control custom-selec text-center filters" data-live-search="true">

                        </select>
                        <label for="municipios"><i class="fa-solid fa-map-location-dot"></i> Municipio</label>
                        <select id="municipios" name="municipio" class="form-control  custom-selec selectpicker  text-center filters" data-live-search="true"></select>

                    </div>

                    <div class="form-group col-sm-12 col-md-6 col-lg-3 text-left">
                        <label for="codigo"><i class="bi bi-qr-code-scan"></i> Código:</label>
                        <input type="text" id="codigo" name="codigo" class="form-control" placeholder="Código de propiedad">
                        <label for="buscar"><i class="fa-solid fa-search"></i> Buscar</label>
                        <button type="submit" class="btn bg-lime-dark w-100" id="buscar">Buscar</button>
                    </div>

                </div>
            </div>
        </div>
</form>
<br>
<!-- Contenedor para mostrar resultados -->
<div id="resultsContainer"></div>

<!-- Controles de paginación -->
<br>
<nav aria-label="Page navigation example">
    <ul id="paginationContainer" class="pagination justify-content-center">
        <!-- Aquí se llenarán dinámicamente los botones de paginación -->
    </ul>
    <div class="paginationControls mb-3 float-right">
        <span id="pageInfo"></span>
        <span id="totalResults"></span>
    </div>
</nav>


<!-- Agrega esta línea para mostrar el total de resultados -->
<br>

<br>
</div>

<!-- Modal -->
<!-- Modal -->

<script>
    const NUM_ITEMS_BY_PAGE = <?php echo NUM_ITEMS_BY_PAGE; ?>;
    let currentPage = 1;
    let totalPages = Math.ceil(<?php echo $num_total_rows; ?> / NUM_ITEMS_BY_PAGE);


    document.addEventListener('DOMContentLoaded', function() {

        const filterForm = document.getElementById('filterForm');
        const resultsContainer = document.getElementById('resultsContainer');
        const paginationContainer = document.getElementById('paginationContainer');
        let totalPages = Math.ceil(<?php echo $num_total_rows; ?> / NUM_ITEMS_BY_PAGE);

        let currentPage = 1;

        const filters = document.querySelectorAll('.filter');
        // Función para generar los botones de paginación
        function generatePaginationButtons() {
            paginationContainer.innerHTML = ''; // Vaciar el contenedor antes de agregar botones

            // Botón "Anterior"
            const prevPageBtn = document.createElement('li');
            prevPageBtn.classList.add('page-item');
            if (currentPage === 1) {
                prevPageBtn.classList.add('disabled');
            }
            const prevPageLink = document.createElement('a');
            prevPageLink.classList.add('page-link');
            prevPageLink.href = '#';
            prevPageLink.setAttribute('aria-label', 'Previous');
            prevPageLink.innerHTML = '<span aria-hidden="true">&laquo;</span>';
            prevPageBtn.appendChild(prevPageLink);
            paginationContainer.appendChild(prevPageBtn);

            // Mostrar un rango de páginas basado en la página actual
            const range = 5; // Número de botones visibles a cada lado de la página actual
            const startPage = Math.max(1, currentPage - Math.floor(range / 2));
            const endPage = Math.min(totalPages, startPage + range - 1);

            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = document.createElement('li');
                pageBtn.classList.add('page-item');
                if (i === currentPage) {
                    pageBtn.classList.add('active');
                }
                const pageLink = document.createElement('a');
                pageLink.classList.add('page-link');
                pageLink.href = '#';
                pageLink.textContent = i;
                pageBtn.appendChild(pageLink);
                paginationContainer.appendChild(pageBtn);
            }

            // Botón "Siguiente"
            const nextPageBtn = document.createElement('li');
            nextPageBtn.classList.add('page-item');
            if (currentPage === totalPages) {
                nextPageBtn.classList.add('disabled');
            }
            const nextPageLink = document.createElement('a');
            nextPageLink.classList.add('page-link');
            nextPageLink.href = '#';
            nextPageLink.setAttribute('aria-label', 'Next');
            nextPageLink.innerHTML = '<span aria-hidden="true">&raquo;</span>';
            nextPageBtn.appendChild(nextPageLink);
            paginationContainer.appendChild(nextPageBtn);
        }

        filters.forEach(filter => {
            filter.addEventListener('change', function() {
                currentPage = 1; // Reiniciar a la primera página al aplicar filtros
                fetchResults(); // Realizar una nueva consulta con los filtros aplicados
                generatePaginationButtons(); // Actualizar el paginador
            });
        });


        // Agregar evento click a los botones de paginación
        paginationContainer.addEventListener('click', function(event) {
            event.preventDefault();
            const target = event.target;

            if (target.classList.contains('page-link')) {
                const pageNumber = parseInt(target.textContent);
                if (!isNaN(pageNumber)) {
                    currentPage = pageNumber;
                    fetchResults();
                    generatePaginationButtons();
                } else if (target.getAttribute('aria-label') === 'Previous' && currentPage > 1) {
                    currentPage--;
                    fetchResults();
                    generatePaginationButtons();
                } else if (target.getAttribute('aria-label') === 'Next' && currentPage < totalPages) {
                    currentPage++;
                    fetchResults();
                    generatePaginationButtons();
                }
            }
        });

        filterForm.addEventListener('submit', function(event) {
            event.preventDefault();
            currentPage = 1; // Reiniciar la página a 1 al enviar el formulario
            fetchResults();
            generatePaginationButtons();
        });

        fetchResults(); // Llamada inicial al cargar la página
        generatePaginationButtons(); // Generar los botones de paginación inicialmente
    })

    function fetchResults() {
        const tipoInmueble = document.getElementById('tipoInmueble').value;
        const estado = document.getElementById('estado').value;
        const habitaciones = document.getElementById('habitaciones').value;
        const piso = document.getElementById('piso').value;
        const codigo = document.getElementById('codigo').value;
        const municipios = document.getElementById('municipios').value;
        const limit = NUM_ITEMS_BY_PAGE;
        const offset = (currentPage - 1) * limit;

        const url =
            `https://adminweb.somospropiedad.com/admin/APIS/get_properties.php?limit=${limit}&offset=${offset}&tipoInmueble=${tipoInmueble}&estado=${estado}&habitaciones=${habitaciones}&piso=${piso}&codigo=${codigo}&municipios=${encodeURIComponent(municipios)}`;
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Obtener la cantidad real de resultados después de aplicar los filtros
                const numResults = data.length;

                // Calcular el número total de páginas
                totalPages = Math.ceil(numResults / NUM_ITEMS_BY_PAGE);

                // Renderizar los resultados, generar los botones de paginación y actualizar la información del paginador
                renderResults(data);
                generatePaginationButtons();
                updatePaginationInfo();
            })
            .catch(error => console.error('Error fetching data:', error));
    }


    function renderResults(data) {
        resultsContainer.innerHTML = '';

        if (data.length === 0) {
            resultsContainer.innerHTML = '<p>No se encontraron resultados.</p>';
            return;
        }

        const cardContainer = document.createElement('div');
        cardContainer.classList.add('row', 'justify-content-center');

        data.forEach(item => {
            const cardCol = document.createElement('div');
            cardCol.classList.add('col-md-6', 'col-lg-6', 'col-sm-12', 'mb-1');

            const card = document.createElement('div');

            // Aplicar formato al valor del canon
            const valorCanonFormatted = new Intl.NumberFormat('es-CO').format(item.valor_canon);

            const cardContent = `
<div class="card" style="width: 100%;">
  <img src="fotos/${item.url_foto_principal}" class="card-img-top" alt="..." style="height:300px">
  <div class="card-body ">
     <button class="btn bg-amber-dark text-left text-white m-1 " type="button"">
            <span class="spinner-grow spinner-grow-sm text-lime-dark " role="status" aria-hidden="true"></span>
            ${item.condicion}
        </button>
  <button 
    class="btn bg-teal-dark text-left text-white m-1" type="button" data-bs-toggle="modal" 
    data-bs-target="#modalInfo${item.codigo}" title="Ver características">
    <i class="fa-solid fa-circle-info"></i> Características
  </button>
    </div>
  <ul class="list-group list-group-flush">
    <li class="list-group-item"> <h5 class="prop-title text-left text-uppercase text-magenta-dark "><b>${item.tipoInmueble} - ${item.codigo}</b></h5></li>
    <li class="list-group-item"><i class="bi bi-geo-alt-fill"></i> ${item.municipio}</li>
    <li class="list-group-item"><i class="bi bi-bar-chart-steps"></i> NIVEL: ${item.nivel_piso}</li>
     <li class="list-group-item"><i class="bi bi-bounding-box"></i> ÁREA: ${item.area} </li>
    <li class="list-group-item"><h5 class="text-magenta-dark"><b>$ ${valorCanonFormatted}</b></h5></li>
  </ul>
  <div class="card-body">
    <a href="#" class=" btn bg-indigo-dark text-white w-100"><i class="bi bi-eye-fill"></i> <b>VER MÁS</b></a>
  
  </div>
</div>

<div class="modal fade" id="modalInfo${item.codigo}" tabindex="-1" aria-labelledby="propertyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="propertyModalLabel"><i class="bi bi-ticket-detailed-fill"></i> Detalles de la Propiedad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="propertyDetails${item.codigo}">
              <div class="row">
                      <div class="col col-lg-4 col-md-4 col-sm-12">
                            <p class="prop-text">
                                <img src="img/icons/bed.png" width="30px" />
                            </p>
                            <p class="prop-text-modal">Alcobas</p>
                            <p class="prop-numb-modal text-magenta-dark "><b>${item.alcobas}</b></p>

                            <p class="prop-text">
                                <img src="img/icons/signage.png" width="30px" />
                            </p>
                            <p class="prop-text-modal">Parqueadero</p>
                            <p class="prop-numb-modal text-magenta-dark "><b>${item.parqueadero}</b></p>

                            <p class="prop-text">
                                <img src="img/icons/patio.png" width="30px" />
                            </p>
                            <p class="prop-text-modalt">Patio</p>
                            <p class="prop-numb-modal text-magenta-dark "><b>${item.patio}</b></p>
                        </div>
                        <div class="col col-md-4">
                            <p class="prop-text">
                                <img src="img/icons/toilet.png" width="30px" />
                            </p>
                            <p class="prop-text-modal">Baños</p>
                            <p class="prop-numb-modal text-magenta-dark "><b>${item.servicios}</b></p>
                            <p class="prop-text">
                                <img src="img/icons/area.png" width="30px" />
                            </p>
                            <p class="prop-text-modal">Área</p>
                            <p class="prop-numb-modal text-magenta-dark "><b>${item.area} m²</b></p>
                            <p class="prop-text">
                                <img src="img/icons/elevator.png" width="30px" />
                            </p>
                            <p class="prop-text-modal">Ascensor</p>
                            <p class="prop-numb-modal text-magenta-dark "><b>${item.ascensor}</b></p>
                        </div>
                        <div class="col col-md-4">
                            <p class="prop-text">
                                <img src="img/icons/kitchen.png" width="30px" />
                            </p>
                            <p class="prop-text-modal">Cocina</p>
                            <p class="prop-numb-modal text-magenta-dark "><b>${item.cocina}</b></p>
                            <p class="prop-text">
                                <img src="img/icons/level.png" width="30px" />
                            </p>
                            <p class="prop-text-modal">Nivel</p>
                            <p class="prop-numb-modal text-magenta-dark "><b>${item.nivel_piso}</b></p>
                            <p class="prop-text">
                                <img src="img/icons/closet.png" width="30px" />
                            </p>
                            <p class="prop-text-modal">Closets</p>
                            <p class="prop-numb-modal text-magenta-dark "><b>${item.closet}</b></p>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-magenta-dark text-white" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
`;


            function openPropertyModal(propertyId) {
                $(`#modalInfo${propertyId}`).modal('show'); // Abre el modal una vez se ha cargado la información
            }


            card.innerHTML = cardContent;
            cardCol.appendChild(card);
            cardContainer.appendChild(cardCol);

        });

        resultsContainer.appendChild(cardContainer);
        resultsContainer.appendChild(cardContainer);
        // Asignar evento click al botón "Ver más" de cada elemento
        const viewDetailsBtns = document.querySelectorAll('.view-details-btn');
        viewDetailsBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const propertyId = this.getAttribute('data-id');
                openPropertyModal(propertyId);
            });
        });
    }



    function updatePaginationInfo() {
        const currentPageSpan = document.getElementById('currentPage');
        const totalPagesSpan = document.getElementById('totalPages');
        const totalResultsSpan = document.getElementById(
            'totalResults'); // Nuevo elemento para mostrar el total de resultados

        if (currentPageSpan && totalPagesSpan && totalResultsSpan) {
            currentPageSpan.textContent = currentPage;
            totalPagesSpan.textContent = totalPages;
            totalResultsSpan.textContent =
                `Total: ${num_total_rows} resultados`; // Actualiza el texto con el total de resultados
        } else {
            console.error('Error: Elements not found.');
        }
    }
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>