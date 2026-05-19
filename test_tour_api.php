<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$key = urlencode($_ENV['TOUR_API_KEY'] ?? '');
$base = 'https://apis.data.go.kr/B551011/KorService2/areaBasedList2';

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$areaCodes = ['1'=>'서울','2'=>'인천','3'=>'대전','4'=>'대구?','5'=>'광주','6'=>'부산','7'=>'울산','8'=>'세종'];

foreach ($areaCodes as $code => $name) {
    $params = http_build_query(['MobileOS'=>'ETC','MobileApp'=>'ttip','_type'=>'json','areaCode'=>$code,'arrange'=>'A','numOfRows'=>'1','pageNo'=>'1']);
    curl_setopt($ch, CURLOPT_URL, "{$base}?serviceKey={$key}&{$params}");
    $body = curl_exec($ch);
    $data = json_decode($body, true);
    $total = $data['response']['body']['totalCount'] ?? 'N/A';
    $sample = '';
    if ($total > 0) {
        $item = $data['response']['body']['items']['item'][0] ?? null;
        $sample = $item ? " → " . ($item['addr1'] ?? '') : '';
    }
    echo "[areaCode={$code} {$name}] totalCount: {$total}{$sample}" . PHP_EOL;
}

curl_close($ch);
