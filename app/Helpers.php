<?php

function helper_titulo_pagina()
{
    return "CAFF";
}

function helper_version_app()
{
    return "0.1";
}

function helper_encrypt($string)
{
    $result = '';
    for ($i = 0; $i < strlen($string); $i++) {
        $char = substr($string, $i, 1);
        $keychar = substr(env('PHP_ENCRYPT_AND_DECRYPT_KEY'), ($i % strlen(env('PHP_ENCRYPT_AND_DECRYPT_KEY'))) - 1, 1);
        $char = chr(ord($char) + ord($keychar));
        $result .= $char;
    }
    return base64_encode($result);
}

function helper_decrypt($string)
{
    $result = '';
    $string = base64_decode($string);
    for ($i = 0; $i < strlen($string); $i++) {
        $char = substr($string, $i, 1);
        $keychar = substr(env('PHP_ENCRYPT_AND_DECRYPT_KEY'), ($i % strlen(env('PHP_ENCRYPT_AND_DECRYPT_KEY'))) - 1, 1);
        $char = chr(ord($char) - ord($keychar));
        $result .= $char;
    }
    return $result;
}

function helper_tipo_perfil_a_font_awesome_icono($tipo_perfil)
{
    $font_awesome_icon = match ($tipo_perfil) {
        'ADMIN' => 'fa-user-shield',
        'BIBLIOTECA' => 'fa-book-open',
        'DOCENTE' => 'fa-chalkboard-teacher',
        'TUTOR' => 'fa-people-roof', //nota: aquí se refiere tanto a socio o padre de familia o tutor*
        'ESTUDIANTE' => 'fa-user-graduate',
        default => 'fa-circle-question'
    };

    return $font_awesome_icon;
}

function helper_abreviar_curso($cadena)
{
    if (!$cadena) {
        return '';
    }

    $cadena = strtoupper(trim($cadena));
    $partes = explode(' ', $cadena);

    // Casos especiales
    $especiales = [
        'TALLER INICIAL ROT' => 'TIR',
        'TALLER INICIAL WEISS' => 'TIW',
        'PRE KINDER ROT' => 'PKR',
        'PRE KINDER WEISS' => 'PKW',
        'KINDER ROT' => 'KR',
        'KINDER WEISS' => 'KW',
    ];

    if (isset($especiales[$cadena])) {
        return $especiales[$cadena];
    }

    // Tablas para los casos regulares
    $cursos = [
        'PRIMERO' => '1',
        'SEGUNDO' => '2',
        'TERCERO' => '3',
        'CUARTO' => '4',
        'QUINTO' => '5',
        'SEXTO' => '6',
    ];

    $niveles = [
        'PRIMARIA' => 'P',
        'SECUNDARIA' => 'S',
    ];

    $paralelos = [
        'ROT' => 'R',
        'WEISS' => 'W',
    ];

    // Búsqueda y armado del resultado
    $curso = $cursos[$partes[0]] ?? '';
    $nivel = $niveles[$partes[2] ?? ''] ?? '';
    $paralelo = $paralelos[$partes[3] ?? $partes[2] ?? ''] ?? '';

    return $curso . $nivel . $paralelo;
}

function helper_recortar_texto($texto, $longitudMaxima)
{
    if (strlen($texto) > $longitudMaxima) {
        return substr($texto, 0, $longitudMaxima - 3) . '...';
    }
    return $texto;
}
