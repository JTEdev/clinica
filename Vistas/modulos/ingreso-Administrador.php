<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>Clinica Medica</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body" style="border-radius:25px ;">
        <p class="login-box-msg">Ingresar como Administrador</p>

        <form method="post">

            <div class="form-group has-feedback">

                <input type="text" class="form-control form-control-lg py-3" name="usuario-Ing" placeholder="Usuario" style="border-radius:25px ;"/>
                <span class="glyphicon glyphicon-user form-control-feedback"></span>

            </div>

            <div class="form-group has-feedback">

                <input type="password" class="form-control form-control-lg py-3" name="clave-Ing" placeholder="Contraseña"  style="border-radius:25px ;"/>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>

            </div>

            <div class="row">

                <!-- /.col -->
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-primary btn-lg text-light my-2 py-3" style="width:100% ; border-radius: 30px; font-weight:600;">Ingresar</button>
                </div>
                <!-- /.col -->
            </div>

            <?php

            $ingreso = new AdminC();
            $ingreso->IngresarAdminC();

            ?>
        </form>

    </div>
    <!-- /.login-box-body -->
</div>