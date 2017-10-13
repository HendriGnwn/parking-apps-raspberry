<?php

require __DIR__ . '/vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;

$response = $data = [];
if (true) {
    $url = "http://localhost/1.jpg";
    //$url = "http://admin:admin1234@192.168.1.64/Streaming/channels/1/picture";
    $filename = date('Ymd-His') .".jpg";
    $img = "files/". $filename;
    file_put_contents($img, file_get_contents($url));
    $type = pathinfo($img, PATHINFO_EXTENSION);
    $data = file_get_contents($img);
   
    $client = new GuzzleHttp\Client(['base_uri' => 'http://localhost/', 'http_errors' => false]);
    $response = $client->request('POST', '/parking-apps/web/api/create-gate-in', [
        'headers' => [
            'Authorization' => 'SnVuZ2xlbGFuZCBJbmRvbmVzaWENCg==',
        ],
        'multipart' => [
            [
                'name'     => 'gate_in_id',
                'contents' => '1',
            ],
            [
                'name'     => 'transport_price_id',
                'contents' => '1',
            ],
            [
                'name'     => 'cameraFileUpload',
                'contents' => fopen($img, 'r'),
                'filename' => $filename,
            ],
        ]
    ]);
    
    $response = json_decode($response->getBody()->getContents(), true);
    if ($response['status'] == 'error') {
        return;
    }
    unlink($img);
    $data = $response['data'];
}

if (false) {
    
    try {
        $connector = new CupsPrintConnector("EPSON_TM-T82-S_A");

        /* Print a "Hello world" receipt" */
        $printer = new Printer($connector);
        
        $printer->setPrintLeftMargin(4);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        
        $printer->setTextSize(3, 1);
        $printer->text($data['app_name']);
        $printer->text("\n");
        $printer->feed(1);
        
        $companyAddress = wordwrap($data['company_address'], 40);
        $printer->setTextSize(1, 1);
        $printer->text($companyAddress);
        $printer->text("\n");
        $printer->feed(1);
        
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(1, 1);
        $printer->text($data['app_name']);
        $printer->text("\n");
        
        $printer->setTextSize(1, 1);
        $printer->text("Date  : " . $data['date']);
        
        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        $printer->setTextSize(3, 1);
        $printer->text("  " . $data['vehicle']);
        $printer->text("\n");
        
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setTextSize(1, 1);
        $printer->text("Time  : " . $data['time']);
        $printer->text("\n");
        
        
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setBarcodeWidth(15);
        $printer->setBarcodeHeight(150);
        $printer->setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);
        $printer->barcode($data['code'], Printer::BARCODE_CODE93);
        $printer->feed(1);
        
        $footerDescription = wordwrap($data['footer_description'], 40);
        $printer->text($footerDescription);
        $printer->feed(1);
        
        $printer->cut();

        /* Close printer */
        $printer -> close();
    } catch (Exception $e) {
        echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
    }
}
