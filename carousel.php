   <?php
    /* Llamar la Cadena de Conexion*/
    include("conexion.php");
    ?>
   <div id="carouselExampleIndicators" class="carousel slide mt-3 mb-3" data-bs-ride="carousel">
       <?php
        $sql_slider = mysqli_query($conn, "SELECT * FROM slider WHERE estado=1 ORDER BY orden");
        $nums_slides = mysqli_num_rows($sql_slider);
        ?>
       <div class="carousel-indicators">
           <?php
            for ($i = 0; $i < $nums_slides; $i++) {
                $active = ($i == 0) ? "active" : "";
            ?>
               <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?php echo $i; ?>" class="<?php echo $active; ?>" aria-current="true" aria-label="Slide <?php echo $i + 1; ?>"></button>
           <?php
            }
            ?>
       </div>
       <div class="carousel-inner">
           <?php
            $isFirstSlide = true;
            while ($rw_slider = mysqli_fetch_array($sql_slider)) {
            ?>
               <div class="carousel-item <?php echo $isFirstSlide ? "active" : ""; ?>">
                   <img src="img/carousel/<?php echo $rw_slider['url_image']; ?>" class="d-block w-100 rounded-3" alt="Slide Image" style="height: auto;">
               </div>
           <?php
                $isFirstSlide = false;
            }
            ?>
       </div>
       <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
           <span class="carousel-control-prev-icon" aria-hidden="true"></span>
           <span class="visually-hidden">Previous</span>
       </button>
       <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
           <span class="carousel-control-next-icon" aria-hidden="true"></span>
           <span class="visually-hidden">Next</span>
       </button>
   </div>
   