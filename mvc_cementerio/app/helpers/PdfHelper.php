<?php
use Dompdf\Dompdf;
use Dompdf\Options;

class PdfHelper {
    public static function generarPlantilla(string $path, array $data, string $filename) {
        if (!file_exists($path)) {
            throw new Exception("La plantilla no existe: $path");
        }
        $html = file_get_contents($path);

        foreach ($data as $key => $value) {
            $html = str_replace("{{ $key }}", htmlspecialchars($value), $html);
        }

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream($filename, ["Attachment" => 1]);
        exit;
    }
}
?>