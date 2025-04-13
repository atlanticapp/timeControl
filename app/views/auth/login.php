<?php include __DIR__ . "/../layouts/header.php"; ?>

<section class="wrapper">
    <div class="form signup">
        <header>Registrarse</header>
        <form id="signupForm" method="post" action="register">
            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" required autocomplete="off">
            <input type="text" class="form-control" id="codigo_empleado" name="codigo_empleado" placeholder="Codigo Empleado" required autocomplete="off">
            <input type="password" class="form-control" id="pwd" name="password" placeholder="Contraseña" required autocomplete="new-password">
            <input type="password" class="form-control" id="confirm_pwd" name="confirm_pwd" placeholder="Confirmar Contraseña" required autocomplete="new-password">
            <div class="relative group rounded-lg md:w-[409.99px] md:h-[60px] bg-gray-50 overflow-hidden before:absolute before:w-12 before:h-12 before:content[''] before:right-0 before:bg-blue-500 before:rounded-full before:blur-lg before:[box-shadow:-60px_20px_10px_10px_#b0d4f9]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" class="w-8 h-8 absolute right-0 -rotate-45 stroke-blue-600 top-1.5 group-hover:rotate-0 duration-300">
                    <path stroke-width="4" stroke-linejoin="round" stroke-linecap="round" fill="none" d="M60.7,53.6,50,64.3m0,0L39.3,53.6M50,64.3V35.7m0,46.4A32.1,32.1,0,1,1,82.1,50,32.1,32.1,0,0,1,50,82.1Z" class="svg-stroke-primary"></path>
                </svg>
                <select id="tipo_usuario" name="tipo_usuario" class="appearance-none relative text-blue-400 bg-transparent ring-0 outline-none border border-neutral-500 text-neutral-900 placeholder-blue-700 text-sm font-bold rounded-lg block w-[352px] h-[55px] p-2.5 
                sm:w-full sm:h-auto sm:p-2.5 
                md:w-[409.99px] md:h-[60px] 
                focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="" disabled selected>Seleccione el tipo de usuario</option>
                    <option value="operador">Operador</option>
                    <option value="supervisor">Supervisor</option>
                    <option value="qa">QA</option>
                </select>
            </div>

            <div class="relative group rounded-lg md:w-[409.99px] md:h-[60px] bg-gray-50 overflow-hidden before:absolute before:w-12 before:h-12 before:content[''] before:right-0 before:bg-blue-500 before:rounded-full before:blur-lg before:[box-shadow:-60px_20px_10px_10px_#b0d4f9]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" class="w-8 h-8 absolute right-0 -rotate-45 stroke-blue-600 top-1.5 group-hover:rotate-0 duration-300">
                    <path stroke-width="4" stroke-linejoin="round" stroke-linecap="round" fill="none" d="M60.7,53.6,50,64.3m0,0L39.3,53.6M50,64.3V35.7m0,46.4A32.1,32.1,0,1,1,82.1,50,32.1,32.1,0,0,1,50,82.1Z" class="svg-stroke-primary"></path>
                </svg>
                <select id="area_id" name="area_id" class="appearance-none relative text-blue-400 bg-transparent ring-0 outline-none border border-neutral-500 text-neutral-900 placeholder-blue-700 text-sm font-bold rounded-lg block w-[352px] h-[55px] p-2.5 
                sm:w-full sm:h-auto sm:p-2.5 
                md:w-[409.99px] md:h-[60px] 
                focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="" disabled selected>Seleccione el Área</option>
                    <?php if (isset($areas) && !empty($areas)): ?>
                        <?php foreach ($areas as $area): ?>
                            <option value="<?= $area['id'] ?>"><?= $area['nombre'] ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <input type="submit" value="Registrarme" />
        </form>
    </div>
    <div class="form login">
        <header>Login</header>
        <form id="loginForm" class="form" method="post" action="login">
            <input type="text" class="form-control" id="codigo_empleado" name="codigo_empleado" placeholder="Codigo Empleado" required autocomplete="off">
            <input type="password" class="form-control" id="pwd" name="password" placeholder="Contraseña" required autocomplete="off">
            <a href="#">Forgot password?</a>
            <input type="submit" value="Login" />
        </form>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch('/timeControl/public/getStatus') // Llama al endpoint de PHP
            .then(response => response.json())
            .then(data => {
                if (data.status && data.message) {
                    const toastrFunction = data.status === "success" ? toastr.success : toastr.error;

                    toastrFunction(data.message, '', {
                        timeOut: 2000
                    });

                    setTimeout(() => {
                        window.location.href = "/timeControl/public/login"; // Limpia la URL
                    }, 2000);
                }
            });
    });


    // Selección de elementos del DOM relacionados con el layout
    const wrapper = document.querySelector(".wrapper"),
        signupHeader = document.querySelector(".signup header"),
        loginHeader = document.querySelector(".login header");

    // Event listeners para cambiar entre las secciones de registro y login
    loginHeader.addEventListener("click", () => {
        wrapper.classList.add("active");
    });

    signupHeader.addEventListener("click", () => {
        wrapper.classList.remove("active");
    });
</script>

<?php include __DIR__ . "/../layouts/footer.php"; ?>