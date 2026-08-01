<?php

namespace App\Services;

use GdImage;

class LogoProcessor
{
    private const MAX_DIMENSION = 800;

    private const TOLERANCIA = 40;

    /**
     * Procesa una imagen de logo: la redimensiona si es muy grande y,
     * opcionalmente, vuelve transparente el fondo sólido detectado en los bordes.
     * Devuelve el contenido binario del PNG resultante.
     */
    public function procesar(string $rutaOrigen, bool $quitarFondo): string
    {
        $imagen = $this->cargar($rutaOrigen);
        $imagen = $this->redimensionarSiEsGrande($imagen);

        if ($quitarFondo) {
            $this->quitarFondo($imagen);
        }

        return $this->aPng($imagen);
    }

    private function cargar(string $ruta): GdImage
    {
        $imagen = imagecreatefromstring(file_get_contents($ruta));
        imagesavealpha($imagen, true);
        imagealphablending($imagen, true);

        return $imagen;
    }

    private function redimensionarSiEsGrande(GdImage $imagen): GdImage
    {
        $ancho = imagesx($imagen);
        $alto = imagesy($imagen);
        $mayor = max($ancho, $alto);

        if ($mayor <= self::MAX_DIMENSION) {
            return $imagen;
        }

        $factor = self::MAX_DIMENSION / $mayor;
        $nuevoAncho = max(1, (int) round($ancho * $factor));
        $nuevoAlto = max(1, (int) round($alto * $factor));

        $redimensionada = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
        imagealphablending($redimensionada, false);
        imagesavealpha($redimensionada, true);
        $transparente = imagecolorallocatealpha($redimensionada, 0, 0, 0, 127);
        imagefill($redimensionada, 0, 0, $transparente);

        imagecopyresampled($redimensionada, $imagen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
        imagedestroy($imagen);

        return $redimensionada;
    }

    /**
     * Flood-fill desde todos los píxeles del borde: cualquier región de color
     * parecido al del borde y conectada a él se vuelve transparente. Así no se
     * borran colores similares que estén dentro del propio logo, solo el fondo.
     */
    private function quitarFondo(GdImage $imagen): void
    {
        imagealphablending($imagen, false);
        imagesavealpha($imagen, true);

        $ancho = imagesx($imagen);
        $alto = imagesy($imagen);
        $colorFondo = imagecolorsforindex($imagen, imagecolorat($imagen, 0, 0));

        $visitado = array_fill(0, $ancho * $alto, false);
        $pila = [];

        for ($x = 0; $x < $ancho; $x++) {
            $pila[] = [$x, 0];
            $pila[] = [$x, $alto - 1];
        }
        for ($y = 0; $y < $alto; $y++) {
            $pila[] = [0, $y];
            $pila[] = [$ancho - 1, $y];
        }

        while ($pila) {
            [$x, $y] = array_pop($pila);

            if ($x < 0 || $x >= $ancho || $y < 0 || $y >= $alto) {
                continue;
            }

            $indice = $y * $ancho + $x;

            if ($visitado[$indice]) {
                continue;
            }
            $visitado[$indice] = true;

            $color = imagecolorsforindex($imagen, imagecolorat($imagen, $x, $y));

            if (! $this->colorParecido($color, $colorFondo)) {
                continue;
            }

            $transparente = imagecolorallocatealpha($imagen, $color['red'], $color['green'], $color['blue'], 127);
            imagesetpixel($imagen, $x, $y, $transparente);

            $pila[] = [$x + 1, $y];
            $pila[] = [$x - 1, $y];
            $pila[] = [$x, $y + 1];
            $pila[] = [$x, $y - 1];
        }
    }

    /**
     * @param  array{red: int, green: int, blue: int}  $a
     * @param  array{red: int, green: int, blue: int}  $b
     */
    private function colorParecido(array $a, array $b): bool
    {
        $distancia = sqrt(
            ($a['red'] - $b['red']) ** 2
            + ($a['green'] - $b['green']) ** 2
            + ($a['blue'] - $b['blue']) ** 2
        );

        return $distancia <= self::TOLERANCIA;
    }

    private function aPng(GdImage $imagen): string
    {
        ob_start();
        imagepng($imagen);
        $contenido = ob_get_clean();
        imagedestroy($imagen);

        return $contenido;
    }
}
