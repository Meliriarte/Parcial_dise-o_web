<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Empleados</title>

  <script>
    function mostrarCampos() {
      //Oculta todos los campos que tienen la clase campo
      var campos = document.getElementsByClassName("campo");
      for (var i = 0; i < campos.length; i++) {
        campos[i].style.display = "none";
      }

      //Se obtiene el valor de la opción seleccionada
      var opcion = document.getElementById("opcion").value;

      //Muestra solo los campos necesarios según la opción elegida
      if (opcion === "1") { //Agregar empleado
        mostrar(["campo_id", "campo_nombre", "campo_apellido", "campo_nacimiento", "campo_ingreso", "campo_tipo"]);
      } else if (opcion === "2") { //Agregar clientes
        mostrar(["campo_id", "campo_clientes"]);
      } else if (opcion === "3") { //Consultar empleado
        mostrar(["campo_id"]);
      } else if (opcion === "4") { //Eliminar empleado
        mostrar(["campo_id", "campo_nombre"]);
      } else if (opcion === "5") { //Obtener salario
        mostrar(["campo_nombre", "campo_apellido"]);
      } else if (opcion === "6") { //Mostrar nómina por tipo
        mostrar(["campo_tipo_nomina"]);
      }
    }

    //Función para mostrar campos específicos según el ID recibido
    function mostrar(listaIds) {
      for (var i = 0; i < listaIds.length; i++) {
        var campo = document.getElementById(listaIds[i]);
        if (campo) {
          campo.style.display = "block";
        }
      }
    }

    //Ejecuta la función al cargar la página
    window.onload = mostrarCampos;
  </script>
</head>
<body>
  <h2>Gestión de empleados</h2>

  <!-- Formulario principal -->
  <form action="empleados.php" method="post">

    <!-- Menú de opciones -->
    <label>Selecciona una opción:</label>
    <select name="opcion" id="opcion" onchange="mostrarCampos()">
      <option value="1">1. Agregar empleados</option>
      <option value="2">2. Agregar clientes</option>
      <option value="3">3. Consultar por identificación</option>
      <option value="4">4. Eliminar empleado</option>
      <option value="5">5. Obtener salario</option>
      <option value="6">6. Mostrar nómina por tipo</option>
      <option value="7">7. Empleado con más clientes</option>
      <option value="8">8. Empleado con mayor salario</option>
    </select>
    <br><br>

    <!-- Campos del formulario, se ocultan o muestran según la opción -->
    <div class="campo" id="campo_id">
      <label>ID:</label>
      <input type="text" name="id">
    </div>

    <div class="campo" id="campo_nombre">
      <label>Nombre:</label>
      <input type="text" name="nombre">
    </div>

    <div class="campo" id="campo_apellido">
      <label>Apellidos:</label>
      <input type="text" name="apellidos">
    </div>

    <div class="campo" id="campo_nacimiento">
      <label>Año de nacimiento:</label>
      <input type="number" name="nacimiento">
    </div>

    <div class="campo" id="campo_ingreso">
      <label>Año de ingreso:</label>
      <input type="number" name="ingreso">
    </div>

    <div class="campo" id="campo_tipo">
      <label>Tipo de empleado:</label>
      <select name="tipo">
        <option value="asalariado">Asalariado</option>
        <option value="comision">Comisión</option>
      </select>
    </div>

    <div class = "campo" id="campo_clientes">
      <label>Clientes:</label>
      <input type="text" name="clientes">
    </div>

    <div class="campo" id="campo_tipo_nomina">
      <label>Mostrar solo:</label>
      <select name="filtroTipo">
        <option value="asalariado">Asalariados</option>
        <option value="comision">Comisión</option>
      </select>
    </div>
    <br>
    <input type="submit" value="Ejecutar">
  </form>
</body>
</html>
