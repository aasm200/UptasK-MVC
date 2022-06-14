<div class="contenedor olvide">
<?php include_once __DIR__.'/../templates/nombre-sitio.php' ?>



<div class="contenedor-sm">
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>  
        <p class="descripcion-pagina"> Reestablece tu password colocando tu email </p>

        <form action="/olvide" class="formulario" method="POST">
        <div class="campo">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="Tu email">
        </div>
   

        <input type="submit" class="boton" value="Reestablecer Password">


        </form>

        <div class="acciones">
            <a href="/crear">No tienes una cuenta? Crea una</a>
            <a href="/">Inicia Sesión</a>

        </div>
    </div> <!-- ContenedorSM -->
</div>