<?php

if (! function_exists('helper_titulo_pagina')) {
    function helper_titulo_pagina()
    {
        return 'CAFF';
    }
}

if (! function_exists('helper_version_app')) {
    function helper_version_app()
    {
        return '0.3';
    }
}

if (! function_exists('helper_encrypt')) {
    function helper_encrypt(string $string)
    {
        $key = config('app.llave_cifrado');

        if (empty($key)) {
            return $string;
        }

        $result = '';

        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, $i % strlen($key), 1);

            $valor_ascii = (ord($char) + ord($keychar)) % 256;
            $result .= chr($valor_ascii);
        }

        return base64_encode($result);
    }
}

if (! function_exists('helper_decrypt')) {
    function helper_decrypt(string $string)
    {
        $key = config('app.llave_cifrado');

        if (empty($key)) {
            return $string;
        }

        $result = '';
        $string = base64_decode($string);

        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, $i % strlen($key), 1);

            $valor_ascii = (ord($char) - ord($keychar) + 256) % 256;
            $result .= chr($valor_ascii);
        }

        return mb_convert_encoding($result, 'UTF-8', 'UTF-8');
    }
}

if (! function_exists('helper_tipo_perfil_a_font_awesome_icono')) {
    function helper_tipo_perfil_a_font_awesome_icono(string $tipo_perfil)
    {
        $font_awesome_icon = match ($tipo_perfil) {
            'ADMIN' => 'fa-user-shield',
            'BIBLIOTECARIA' => 'fa-book-open',
            'DOCENTE' => 'fa-chalkboard-teacher',
            'TUTOR' => 'fa-people-roof', //nota: aquí se refiere tanto a socio o padre de familia o tutor*
            'ESTUDIANTE' => 'fa-user-graduate',
            default => 'fa-circle-question'
        };

        return $font_awesome_icon;
    }
}

if (! function_exists('helper_abreviar_curso')) {
    function helper_abreviar_curso(string $cadena)
    {
        if (! $cadena) {
            return '';
        }

        $cadena = strtoupper(trim($cadena));
        $partes = explode(' ', $cadena);

        // Casos especiales
        $especiales = [
            'JARDIN MATERNAL ROT' => 'JMR',
            'JARDIN MATERNAL WEISS' => 'JMW',
            'JARDIN INFANTIL ROT' => 'JIR',
            'JARDIN INFANTIL WEISS' => 'JIW',
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
}

if (! function_exists('helper_recortar_texto')) {
    function helper_recortar_texto(string $texto, int $longitudMaxima)
    {
        if (strlen($texto) > $longitudMaxima) {
            return substr($texto, 0, $longitudMaxima - 3) . '...';
        }

        return $texto;
    }
}

if (! function_exists('helper_dia_semana_a_nombre')) {
    function helper_dia_semana_a_nombre(int $dia_semana)
    {
        $dias = [
            1 => 'LUNES',
            2 => 'MARTES',
            3 => 'MIÉRCOLES',
            4 => 'JUEVES',
            5 => 'VIERNES',
            6 => 'SÁBADO',
            7 => 'DOMINGO',
        ];

        return $dias[$dia_semana] ?? 'Desconocido';
    }
}
