<?php
goto st;

// API endpoint URL
$url = 'http://api.deepdream.ir/parsic/api/whatsapp?apikey=ParsicAPI02';

// API key
$apikey = 'ParsicAPI02';

// Data to be sent in the POST request
$data = array(
    'id' => '33700',
);

// Set up cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

// If your API requires an API key in the header, you can add it like this:
// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', 'apikey: ' . $apikey));

// Execute cURL request and get the response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
}

// Close cURL session
curl_close($ch);

// Display the API response
echo $response;


exit();
$pdf = file_get_contents(__DIR__."/samplePDF.txt");
$pdf = base64_decode($pdf);
file_put_contents(__DIR__."/dd.pdf", $pdf);



st:
require_once "vendor/autoload.php";
$pdf = new CzProject\PdfRotate\PdfRotate;
$sourceFile = __DIR__."/dd.pdf";
$outputFile = __DIR__."/dd.pdf";

$pdf->rotatePdf($sourceFile, $outputFile, $pdf::DEGREES_270);