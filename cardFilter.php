<?php
require('conexion.php');
session_start();
header('Access-Control-Allow-Origin: *');

define('NUM_ITEMS_BY_PAGE', 9);
$sql_count = "SELECT COUNT(*) as total_products FROM proprieter
INNER JOIN municipios ON proprieter.Municipio = municipios.id_municipio
WHERE codigo LIKE '%$buscar%' AND nombre_inquilino = '' AND estadoPropietario = 'ACTIVO'";

$result_count = $con->query($sql_count);
$row_count = $result_count->fetch_assoc();
$num_total_rows = $row_count['total_products'];
$canonFormateado = number_format($row['valor_canon'], 0, ',', '.');

?>

<style>
    /* Personalización del modal */
    .modal {
        z-index: 1500;
        /* Asegúrate de que el z-index del modal sea mayor que el del fondo opaco */
    }

    /* Personalización del fondo opaco */
    .modal-backdrop {
        z-index: -1;
        /* Ajusta el z-index del fondo opaco según sea necesario */
    }

    .prop-state {
        margin: 0;
        display: inline;
        background: #123960;
        color: #ffffff;
        padding: .2em 1em;
        border-radius: 15px;
        top: 13em;
        left: 1em;

        -webkit-box-shadow: 2px 2px 4px 1px rgba(0, 0, 0, 0.5);
        -moz-box-shadow: 2px 2px 4px 1px rgba(0, 0, 0, 0.5);
        box-shadow: 2px 2px 4px 1px rgba(0, 0, 0, 0.5);
    }


    .prop-title {
        margin: 0;
        font-size: 1.5em;
        font-weight: bold;
        color: #123960;
        margin: .4em 0;
        text-transform: uppercase;
    }

    .prop-address {
        font-size: 1.0em;
        margin: .5em 0;
        text-align: left;
    }

    .prop-price {
        font-size: 2.5em;
        font-weight: bold;
        color: #123960;
        margin: .4em 0;
    }

    .prop-aditional {
        display: flex;

    }

    .additional-sec {
        width: 33%;

    }

    .prop-numb {
        margin: 0;
        font-size: 1.2em;
        font-weight: bold;
        color: #123960;
        margin: .4em 0;
    }

    .prop-text {
        margin: 0;
        font-size: 1.5em;
        font-weight: bold;
        color: #000;
        margin: .4em 0;
    }

    .colorTexto {
        color: #123960;
    }

    .fa-hablemos {
        display: inline-block;
        width: 24px;
        height: 24px;
        background-image: url('./images/icons/icon.png');
        background-size: cover;
    }

    .grid-item {
        width: 25%;
    }

    .grid-item--width2 {
        width: 50%;
    }

    .boton {
        background-color: #123960;
        cursor: none;
        color: #ffffff;
        display: inline-block;
    }

    .colorActivo {
        color: #48fb47;
        display: inline-block;
    }

    @media (max-width: 10px) {
        .foto {
            display: block;
            /* Mostrar el elemento en tamaños menores a 320px */
        }
    }

    .filters {
        cursor: pointer;
    }

    .prop-numb-modal {
        margin: 0;
        font-size: 1.0em;
        font-weight: bold;
        color: #123960;
        margin: .4em 0;
    }

    .prop-text-modal {
        margin: 0;
        font-size: 1.2em;
        font-weight: bold;
        color: #000;
        margin: .4em 0;

    }

    img[tabindex="0"]:focus {
        position: fixed;
        z-index: 1;
        width: 500px;
        height: 500px;
        top: 50%;
        left: 50%;
        transform: translate3d(-50%, -50%, 0);
        margin: auto;
        box-shadow: 0 0 20px #000, 0 0 0 1000px rgba(18, 57, 96, .8);
        outline: 2px solid #fff;
    }

    img[tabindex="0"]:focus,
    img[tabindex="0"]:focus~* {
        pointer-events: none;
        transition: width .3s, height .3s .1s;
    }
</style>

<div class="p-3 m-3">
    <!-- Formulario de filtros -->
    <form id="filterForm">
        <div class="card  text-center p-3 m-3">

            <div class="card rounded-bottom"">
        <div class=" card-header boton ">
            <i class=" fa-solid fa-filter"></i> REALIZAR CONSULTA PERSONALIZADA
            </div>
            <br>
            <div class="card-body">
                <div class="row w-100 d-flex justify-content-center">
                    <div class="form-group col-sm-12 col-md-6 col-lg-2 text-left">

                        <label for="tipoInmueble">Tipo </label>
                        <select class="form-control" id="tipoInmueble" name="tipoInmueble">
                            <option value="">Seleccionar <i class="fa-solid fa-hand-pointer"></i></option>
                            <option value="Casa">Casa</option>
                            <option value="Apartamento">Apartamento</option>
                            <option value="Local">Local</option>
                            <option value="Apartaestudio">Apartaestudio</option>
                            <option value="Penthouse">Penthouse</option>
                            <option value="Finca">Finca</option>
                            <option value="Casa con local">Casa con local</option>
                            <option value="LOTE">Lote</option>
                        </select>
                        <label for="estado">Estado</label>
                        <select class="form-control" id="estado" name="estado">
                            <option value="">Seleccionar</option>
                            <option value="EN ALQUILER">EN ALQUILER</option>
                            <option value="EN VENTA">EN VENTA</option>
                            <option value="ALQUILER O VENTA">ALQUILER O VENTA</option>
                        </select>

                    </div>



                    <div class="form-group col-sm-12 col-md-6 col-lg-2 text-left">
                        <label for="piso"><i class="fa fa-level-up" aria-hidden="true"></i> Nivel</label>
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
                        <label for="habitaciones"><i class="fa fa-bed" aria-hidden="true"></i> Habitaciones</label>
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

                    <div class="form-group col-sm-12 col-md-6 col-lg-2 text-left ">
                        <label for="lista_departamento"><i class="fa-solid fa-map-location-dot"></i> Departamento</label>
                        <select id="lista_departamento" name="departamento" class="form-control form-select-lg custom-selec text-center filters" data-live-search="true">

                        </select>
                        <label for="municipios"><i class="fa-solid fa-map-location-dot"></i> Municipio</label>
                        <select id="municipios" name="municipio" class="form-control form-select-lg custom-selec selectpicker  text-center filters" data-live-search="true"></select>

                    </div>

                    <div class="form-group col-sm-12 col-md-6 col-lg-2 text-left">
                        <label for="codigo">Código:</label>
                        <input type="text" id="codigo" name="codigo" class="form-control" placeholder="Código de propiedad">
                        <label for="buscar"><i class="fa fa-search" aria-hidden="true"></i> Buscar</label>
                        <button type="submit" class="btn btn-success w-100" id="buscar">Buscar</button>
                    </div>

                </div>
            </div>
    </form>

</div>
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
<div class="modal fade" id="propertyModal" tabindex="-1" role="dialog" aria-labelledby="propertyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="propertyModalLabel">Detalles de la Propiedad</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="propertyDetails">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    const NUM_ITEMS_BY_PAGE = <?php echo NUM_ITEMS_BY_PAGE; ?>;
    let currentPage = 1;
    let totalPages = Math.ceil(<?php echo $num_total_rows; ?> / NUM_ITEMS_BY_PAGE);

    document.addEventListener('DOMContentLoaded', function() {

        const filterForm = document.getElementById('filterForm');
        const resultsContainer = document.getElementById('resultsContainer');
        const paginationContainer = document.getElementById('paginationContainer');
        const totalPages = <?php echo ceil($num_total_rows / NUM_ITEMS_BY_PAGE); ?>;
        let currentPage = 1;

        const filters = document.querySelectorAll('.filter');
        filters.forEach(filter => {
            filter.addEventListener('change', function() {
                currentPage = 1; // Reiniciar a la primera página al aplicar filtros
                fetchResults(); // Realizar una nueva consulta con los filtros aplicados
                generatePaginationButtons(); // Actualizar el paginador
            });
        });
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

            // Botones numéricos
            for (let i = 1; i <= totalPages; i++) {
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
            `https://adminweb.somospropiedad.com/dashboard/get_properties.php?limit=${limit}&offset=${offset}&tipoInmueble=${tipoInmueble}&estado=${estado}&habitaciones=${habitaciones}&piso=${piso}&codigo=${codigo}&municipios=${encodeURIComponent(municipios)}`;

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
            cardCol.classList.add('col-md-6', 'col-lg-4', 'col-sm-12', 'mb-1');

            const card = document.createElement('div');
            card.classList.add('card');
            // Aplicar formato al valor del canon
            const valorCanonFormatted = new Intl.NumberFormat('es-CO').format(item.valor_canon);

            const cardContent = `
    <img src="${item.foto1}" class="card-img-top" style="height:300px" alt="Imagen de la propiedad">
    <div class="card-body">
        <button class="btn boton text-left" type="button"">
            <span class="spinner-grow spinner-grow-sm colorActivo" role="status" aria-hidden="true"></span>
            ${item.condicion}
        </button>
        <button class="btn boton text-left" type="button" data-toggle="modal" data-target="#modalInfo${item.codigo}" title="Ver características">
            <i class="fa-solid fa-circle-info"></i> Caracteristicas
        </button>
        <div class="row">
         <div class="col col-md-12 col-lg-12 col-sm-12 text-left">
    <h5 class="prop-title text-left text-uppercase">${item.tipoInmueble} - ${item.codigo}</h5>
        <p class="prop-text text-left text-uppercase">${item.municipio}</p>
        <p class="prop-text text-left text-uppercase">NIVEL: ${item.nivel_piso}</p>
        <p class="prop-text text-left text-uppercase">ÁREA: ${item.area} m<sup>2</sub></p>
        <p class="prop-text text-left ">$${valorCanonFormatted}</p>
         </div>
         
        </div>
    
      <a class="btn btn-outline-success btn-md w-100" boton href="viewInformation.php?nik=${item.codigo}"><i class="fa fa-eye" aria-hidden="true"></i> Ver más</a>
      </div>
    <div class="modal fade" id="modalInfo${item.codigo}" tabindex="-1" role="dialog" aria-labelledby="modalInfoTitle${item.codigo}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="prop-text-modal text-center" id="modalInfoTitle${item.codigo}">Información detallada de la propiedad <b style="color:red">${item.codigo}</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Contenido del modal con información detallada -->
                    <div class="row">
                        <div class="col col-lg-4 col-md-4 col-sm-12">
                            <p class="prop-text">
                                <img src="../images/icons/bed.png" width="30px" />
                            </p>
                            <p class="prop-text-modal">Alcobas</p>
                            <p class="prop-numb-modal">${item.alcobas}</p>

                            <p class="prop-text">
                                <img src="../images/icons/signage.png" width="30px" />
                            </p>
                            <p class="prop-text-modal">Parqueadero</p>
                            <p class="prop-numb-modal">${item.parqueadero}</p>

                            <p class="prop-text">
                                <img src="../images/icons/patio.png" width="30px" />
                            </p>
                            <p class="prop-text-modalt">Patio</p>
                            <p class="prop-numb-modal">${item.patio}</p>
                        </div>
                        <div class="col col-md-4">
                            <p class="prop-text">
                                <img src="../images/icons/toilet.png" width="30px" />
                            </p>
                            <p class="prop-text-modal">Baños</p>
                            <h4 class="prop-numb-modal">${item.servicios}</h4>
                            <p class="prop-text">
                                <img src="../images/icons/area.png" width="30px" />
                            </p>
                            <p class="prop-text-modal">Área</p>
                            <h4 class="prop-numb-modal">${item.area} m²</h4>
                            <p class="prop-text">
                                <img src="../images/icons/elevator.png" width="30px" />
                            </p>
                            <p class="prop-text-modal">Ascensor</p>
                            <h4 class="prop-numb-modal">${item.ascensor}</h4>
                        </div>
                        <div class="col col-md-4">
                            <p class="prop-text">
                                <img src="../images/icons/kitchen.png" width="30px" />
                            </p>
                            <p class="prop-text-modal">Cocina</p>
                            <p class="prop-numb-modal">${item.cocina}</p>
                            <p class="prop-text">
                                <img src="../images/icons/level.png" width="30px" />
                            </p>
                            <p class="prop-text-modal">Nivel</p>
                            <p class="prop-numb-modal">${item.nivel_piso}</p>
                            <p class="prop-text">
                                <img src="../images/icons/closet.png" width="30px" />
                            </p>
                            <p class="prop-text-modal">Closets</p>
                            <p class="prop-numb-modal">${item.closet}</p>
                        </div>
                    </div>
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