<?php

require __DIR__ . '/vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;

if (false) {
    $url = "http://admin:admin1234@192.168.1.64/Streaming/channels/1/picture";
    $img = "test.jpg";
    file_put_contents($img, file_get_contents($url));
}

if (true) {
    
    try {
        $connector = new CupsPrintConnector("EPSON_TM-T82-S_A");

        /* Print a "Hello world" receipt" */
        $printer = new Printer($connector);
        
        $printer->setPrintLeftMargin(4);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        
        $printer->setTextSize(3, 1);
        $printer->text("PARKING TICKET");
        $printer->text("\n");
        $printer->feed(1);
        
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setTextSize(1, 1);
        $printer->text("JUNGLELAND INDONESIA");
        $printer->text("\n");
        
        $printer->setTextSize(1, 1);
        $printer->text("Date  : " . strftime('%d %b %Y', strtotime(date('Y-m-d'))));
        
        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        $printer->setTextSize(3, 1);
        $printer->text("  MOTOR");
        $printer->text("\n");
        
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setTextSize(1, 1);
        $printer->text("Time  : " . date('H:i:s'));
        $printer->text("\n");
        
        
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setBarcodeWidth(15);
        $printer->setBarcodeHeight(150);
        $printer->setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);
        $printer->barcode("11167681272999", Printer::BARCODE_CODE93);
        $printer->feed(1);
        
        $printer->text("SELAMAT DATANG DI");
        $printer->text("\n");
        $printer->text("JUNGLELAND INDONESIA");
        $printer->text("\n");
        $printer->text("SIMPAN TIKET INI DENGAN AMAN");
        
        $printer->feed(1);
        
        $printer->cut();

        /* Close printer */
        $printer -> close();
    } catch (Exception $e) {
        echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
    }
}
