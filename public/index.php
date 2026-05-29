<?php

require __DIR__ . '/../vendor/autoload.php';

$app = Slim\Factory\AppFactory::create();

$app->get('/simulasi-nested', function ($request, $response) {

    $data = [
        "id_resep" => 101,
        "nama_pasien" => "Budi Santoso",
        "dokter" => "dr. Andi",
        "tanggal" => "2026-03-04",
        "status" => "diproses",
        "detail_obat" => [
            [
                "id_obat" => 12,
                "nama_obat" => "Amoxicillin",
                "jumlah" => 10,
                "dosis" => "3x1 sesudah makan"
            ],
            [
                "id_obat" => 45,
                "nama_obat" => "Paracetamol",
                "jumlah" => 5,
                "dosis" => "1x1 jika demam"
            ]
        ]
    ];

    $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));

    return $response
        ->withHeader('Content-Type', 'application/json');
});

$app->run();