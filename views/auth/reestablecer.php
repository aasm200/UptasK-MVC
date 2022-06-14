<div class="contenedor reestablecer">
<?php include_once __DIR__.'/../templates/nombre-sitio.php' ?>

    <div class="contenedor-sm">
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <?php if($mostrar) { ?>

        <p class="descripcion-pagina"> Coloca tu nuevo Password</p>

        <form  class="formulario" method="POST">
       
        <div class="campo">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" placeholder="Tu Password">
        </div>



        <input type="submit" class="boton" value="Guardar Password">


        </form>

        <div class="acciones">
            <a href="/">Ya tienes una cuenta?Inicia Sesión</a>
            <a href="/crear">No tienes una cuenta? Crea una</a>

        </div>
    </div> <!-- ContenedorSM -->
    <?php } ?>
</div>