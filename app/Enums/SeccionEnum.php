<?php

namespace App\Enums;

enum SeccionEnum: string
{
    case ConsultaProductos = 'Consulta de productos';
    case ConsultaUsuarios = 'Consulta de usuarios';
    case AltaProductos = 'Alta de productos';
    case AltaUsuario = 'Alta de usuario';
    case PerfilesUsuarios = 'Perfiles de usuarios';
}
