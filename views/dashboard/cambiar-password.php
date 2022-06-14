<?php include_once __DIR__. '/header-dashboard.php'; ?>

<div class="contenedor-sm">
    <?php include_once __DIR__.'/../templates/alertas.php'; ?>

    <a href="/perfil" class="enlace">Cambiar Perfil</a>


    <form class="formulario" method="POST" action="/cambiar-password">
        <div class="campo">
            <label for="password_actual">Password Actual</label>
            <input type="password" name="password_actual" placeholder="Tu password" >
        </div>

        <div class="campo">
            <label for="password_nuevo">Password nuevo</label>
            <input type="password" name="password_nuevo" placeholder="Tu nuevo password" >
        </div>

        <input type="submit" value="Guardar cambios">
    </form>
</div>





<?php include_once __DIR__. '/footer-dashboar.php'; ?>