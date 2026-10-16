<?php
require_once __DIR__ . '/../model/Circunferencia.php';

class CircunferenciaController
{
    private Circunferencia $modelo;

    public function __construct()
    {
        $this->modelo = new Circunferencia();
    }

    public function procesarFormulario(): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return '';
        }

        return match ($_POST['accion'] ?? '') {
            'guardar'  => $this->guardarOActualizar(),
            'eliminar' => $this->eliminar(),
            default    => '',
        };
    }

    private function guardarOActualizar(): string
    {
        $id      = (int) ($_POST['id_circunferencia'] ?? 0);
        $detalle = mb_substr(trim($_POST['txtDetalle'] ?? ''), 0, 255);
        $radio   = str_replace(',', '.', trim($_POST['txtRadio'] ?? ''));

        if ($radio === '' || !is_numeric($radio) || (float) $radio <= 0) {
            return 'Ingresa un radio mayor a cero';
        }
        if ((float) $radio > 1000000) {
            return 'El radio no puede superar 1 000 000';
        }

        if ($id > 0) {
            $this->modelo->actualizar($id, $detalle, (float) $radio);
            header('Location: home.php?mensaje=' . urlencode('Registro actualizado'));
        } else {
            $this->modelo->guardar($detalle, (float) $radio);
            header('Location: home.php?mensaje=' . urlencode('Registro guardado'));
        }
        exit;
    }

    private function eliminar(): string
    {
        $id = (int) ($_POST['id_circunferencia'] ?? 0);
        if ($id <= 0) {
            return 'No se pudo eliminar el registro';
        }

        $this->modelo->eliminar($id);
        header('Location: home.php?mensaje=' . urlencode('Registro eliminado'));
        exit;
    }

    public function listar(string $buscar = ''): array
    {
        return $this->modelo->listar($buscar);
    }

    public function obtenerParaEditar(int $id): ?array
    {
        return $id > 0 ? $this->modelo->obtenerPorId($id) : null;
    }
}