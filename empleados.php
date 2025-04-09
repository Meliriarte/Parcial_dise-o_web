<?php
//Inicia sesión para mantener empleados guardados entre solicitudes
session_start(); 

//Clase base abstracta
abstract class Empleado {
    protected $id, $nombre, $apellidos, $nacimiento, $ingreso;

    public function __construct($id, $nombre, $apellidos, $nacimiento, $ingreso) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->nacimiento = $nacimiento;
        $this->ingreso = $ingreso;
    }

    //Retorna el ID del empleado
    public function getid() {
        return $this->id;
    }

    //Nombre completo
    public function getnombrecompleto() {
        return $this->nombre . " " . $this->apellidos;
    }

    //Calcula los años que lleva en la empresa
    public function getañosenlaempresa() {
        return date("Y") - $this->ingreso;
    }

    //Métodos obligatorios en las clases hijas
    abstract public function calcularsalario();
    abstract public function gettipo();
}

//Clase EAsalariado hereda de Empleado
class EAsalariado extends Empleado {
    //Salario base fijo para asalariados
    private $salarioBase = 1500000; 

    //Calcula el salario de un asalariado basado en los años que lleva en la empresa
    public function salarioasalariado() {
        $años = $this->getañosenlaempresa();
        $incremento = 0;

        if ($años <= 3) {
            $incremento = 0.05;
        } elseif ($años <= 7) {
            $incremento = 0.10;
        } elseif ($años <= 15) {
            $incremento = 0.15;
        } else {
            $incremento = 0.20;
        }

        //Calcula el salario base con el incremento correspondiente
        return $this->salarioBase * (1 + $incremento);
    }

    public function calcularsalario() {
        return $this->salarioasalariado();
    }

    public function gettipo() {
        return "asalariado";
    }
}

//Clase EComision hereda de Empleado
class EComision extends Empleado {
    private $clientes;
    //Monto fijo por cliente
    private $montoCliente = 130000; 
    //Salario asegurado para comisionistas
    private $salarioBase = 1300282;

    public function __construct($id, $nombre, $apellidos, $nacimiento, $ingreso, $clientes = 0) {
        parent::__construct($id, $nombre, $apellidos, $nacimiento, $ingreso);
        $this->clientes = $clientes;
    }

    public function salariocomisionista() {
        $salario = $this->montoCliente * $this->clientes;
        return ($salario < $this->salarioBase) ? $this->salarioBase : $salario;
    }

    public function calcularsalario() {
        return $this->salariocomisionista();
    }

    public function agregarclientes($cantidad) {
        $this->clientes += $cantidad;
    }

    public function getclientes() {
        return $this->clientes;
    }

    public function gettipo() {
        return "comision";
    }
}


//Se inicializa el arreglo de empleados si no existe
if (!isset($_SESSION["empleados"])) {
    $_SESSION["empleados"] = [];
}
//Se obtiene el arreglo de empleados de la sesión
$empleados = &$_SESSION["empleados"];

//Funciones auxiliares
//Funcion para buscar un empleado por su ID
function buscarempleados($id) {
    global $empleados;
    foreach ($empleados as $e) {
        if ($e->getid() == $id) {
            return $e;
        }
    }
    return null;
}

//Funcion para eliminar un empleado por su ID
function eliminarempleado($id) {
    global $empleados;
    foreach ($empleados as $i => $e) {
        if ($e->getid() == $id) {
            unset($empleados[$i]);
            $empleados = array_values($empleados);
            return true;
        }
    }
    return false;
}

//Opcion escogida en el formulario
$opcion = $_POST["opcion"];

switch ($opcion) {
    case "1": // Agregar empleado
        $id = $_POST["id"];
        $nombre = $_POST["nombre"];
        $apellidos = $_POST["apellidos"];
        $nacimiento = $_POST["nacimiento"];
        $ingreso = $_POST["ingreso"];
        $tipo = $_POST["tipo"];
    
        if ($tipo == "asalariado") {
            //Solo requiere datos basicos el salario esta definido
            $empleado = new EAsalariado($id, $nombre, $apellidos, $nacimiento, $ingreso);
        } else {
            //Inicia con 0 clientes y el salario base asegurado
            $empleado = new EComision($id, $nombre, $apellidos, $nacimiento, $ingreso);
        }
    
        $empleados[] = $empleado;
        echo "Empleado agregado exitosamente.";
        break;

    case "2": //Agregar clientes a un empleado
        $id = $_POST["id"];
        $cantidad = $_POST["clientes"];
        $emp = buscarempleados($id);
        if ($emp && $emp instanceof EComision) {
            $emp->agregarclientes($cantidad);
            echo "Clientes agregados exitosamente.";
        } else {
            echo "El empleado no se encuentra o no es de comision.";
        }
        break;

    case "3": //Consultar empleado por ID
        $id = $_POST["id"];
        $emp = buscarempleados($id);
        if ($emp) {
            echo "ID: " . $emp->getid() . "<br>";
            echo "Nombre: " . $emp->getnombrecompleto() . "<br>";
            echo "Tipo: " . ucfirst($emp->gettipo()) . "<br>";
            echo "Salario: $" . number_format($emp->calcularsalario()) . "<br>";
        } else {
            echo "Empleado no encontrado.";
        }
        break;

    case "4": //Eliminar empleado
        $id = $_POST["id"];
        if (eliminarempleado($id)) {
            echo "Empleado eliminado exitosamente.";
        } else {
            echo "Empleado no encontrado.";
        }
        break;

    case "5": //Obtener salario
        $id = $_POST["id"];
        $emp = buscarempleados($id);
        if ($emp) {
            //Se cambia el formato de salario a pesos
            echo "Salario: $" . number_format($emp->calcularsalario());
        } else {
            echo "Empleado no encontrado.";
        }
        break;

    case "6": //Mostrar nómina discriminada por tipo
        $tipo = $_POST["filtroTipo"];
        $encontrados = 0;

        foreach ($empleados as $e) {
            if ($tipo == "todos" || $e->gettipo() == $tipo) {
                echo $e->getnombrecompleto() . " - $" . number_format($e->calcularsalario()) . " (" . ucfirst($e->gettipo()) . ")<br>";
                $encontrados++;
            }
        }

        if ($encontrados == 0) {
            echo "No hay empleados del tipo seleccionado.";
        }
        break;

    case "7": //Empleado con más clientes
        $mayor = null;
        foreach ($empleados as $e) {
            if ($e instanceof EComision) {
                if (!$mayor || $e->getclientes() > $mayor->getclientes()) {
                    $mayor = $e;
                }
            }
        }

        if ($mayor) {
            echo "El empleado con más clientes es: " . $mayor->getnombrecompleto() . " <br> Con " . $mayor->getclientes() . " clientes.";
        } else {
            echo "No hay empleados por comisión.";
        }
        break;

    case "8": // Empleado con mayor salario
        $mayor = null;
        foreach ($empleados as $e) {
            if (!$mayor || $e->calcularsalario() > $mayor->calcularsalario()) {
                $mayor = $e;
            }
        }

        if ($mayor) {
            echo "El empleado con mayor salario es: " . $mayor->getnombrecompleto() . " <br> Con salario de $" . number_format($mayor->calcularsalario());
        } else {
            echo "No hay empleados registrados.";
        }
        break;
}
//Boton para volver al formulario
echo " <br> <button type='button' onclick='location.href=\"formulario.php\"'>Volver al formulario</button>";
?>