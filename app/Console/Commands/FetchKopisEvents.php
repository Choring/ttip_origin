<?php

namespace App\Console\Commands;

use App\Models\CulturalEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchKopisEvents extends Command
{
    protected $signature = 'app:fetch-kopis-events';
    protected $description = 'KOPIS API에서 대구 공연/전시 정보를 수집하여 DB에 저장';

    private string $baseUrl = 'https://www.kopis.or.kr/openApi/restful';

    public function handle(): int
    {
        $this->info('KOPIS 대구 공연/전시 데이터 수집 시작...');

        $apiKey = config('services.kopis.api_key');
        if (!$apiKey) {
            $this->error('KOPIS_API_KEY가 설정되지 않았습니다. .env에 KOPIS_API_KEY를 추가해주세요.');
            return 1;
        }

        $stdate  = now()->subMonths(3)->format('Ymd'); // 3개월 전부터
        $eddate  = now()->addYear()->format('Ymd');    // 1년 후까지

        $page    = 1;
        $perPage = 100;
        $saved   = 0;

        do {
            $url = "{$this->baseUrl}/pblprfr?" . http_build_query([
                'service'    => $apiKey,
                'stdate'     => $stdate,
                'eddate'     => $eddate,
                'cpage'      => $page,
                'rows'       => $perPage,
                'signgucode' => '27', // 대구광역시
                'newsql'     => 'Y',
            ]);

            try {
                $response = Http::connectTimeout(5)->timeout(30)->withoutVerifying()->get($url);
            } catch (\Exception $e) {
                $this->error("API 연결 오류 (page {$page}): " . $e->getMessage());
                Log::error("FetchKopisEvents 연결 오류 (page {$page}): " . $e->getMessage());
                break;
            }

            if (!$response->successful()) {
                $this->error("API 요청 실패 (page {$page}): HTTP " . $response->status());
                Log::warning("FetchKopisEvents 요청 실패 (page {$page}): HTTP " . $response->status());
                break;
            }

            $xml = @simplexml_load_string($response->body());
            if ($xml === false) {
                $this->warn("XML 파싱 실패 (page {$page})");
                break;
            }

            $items = $xml->db ?? [];
            $count = count($items);

            if ($count === 0) break;

            foreach ($items as $item) {
                $mt20id = trim((string) ($item->mt20id ?? ''));
                if (!$mt20id) continue;

                $eventSeq  = 'kopis_' . $mt20id;
                $startDate = $this->parseKopisDate((string) ($item->prfpdfrom ?? ''));
                $endDate   = $this->parseKopisDate((string) ($item->prfpdto   ?? ''));

                if (!$startDate || !$endDate) continue;

                $subject = trim((string) ($item->prfnm    ?? ''));
                $place   = trim((string) ($item->fcltynm  ?? '')) ?: null;
                $poster  = trim((string) ($item->poster   ?? '')) ?: null;
                $genrenm = trim((string) ($item->genrenm  ?? ''));
                $gubun   = $this->mapGenreToGubun($genrenm);

                // 처음 저장하거나 content가 없는 경우에만 상세 API 호출
                $existing    = CulturalEvent::find($eventSeq);
                $needsDetail = !$existing || !$existing->content;

                $data = [
                    'subject'     => $subject,
                    'event_gubun' => $gubun,
                    'start_date'  => $startDate,
                    'end_date'    => $endDate,
                    'place'       => $place,
                    'image'       => $poster,
                ];

                if ($needsDetail) {
                    $detail = $this->fetchDetail($mt20id, $apiKey);
                    if ($detail) {
                        $data['pay']      = $detail['pay'];
                        $data['content']  = $detail['content'];
                        $data['homepage'] = $detail['homepage'];
                    }
                    usleep(150000); // 0.15초 딜레이 (API 부하 방지)
                }

                CulturalEvent::updateOrCreate(
                    ['event_seq' => $eventSeq],
                    $data
                );

                $saved++;
            }

            $this->info("  Page {$page}: {$count}건 처리 (누적 {$saved}건 저장/업데이트)");
            $page++;

        } while ($count >= $perPage);

        $this->info("완료! 총 {$saved}건 저장/업데이트");
        return 0;
    }

    /** 공연 상세 정보 조회 (pay, content, homepage) */
    private function fetchDetail(string $mt20id, string $apiKey): ?array
    {
        $url = "{$this->baseUrl}/pblprfr/{$mt20id}?" . http_build_query([
            'service' => $apiKey,
            'newsql'  => 'Y',
        ]);

        try {
            $response = Http::connectTimeout(5)->timeout(15)->withoutVerifying()->get($url);
            if (!$response->successful()) return null;

            $xml = @simplexml_load_string($response->body());
            if ($xml === false || !isset($xml->db)) return null;

            $db = $xml->db;

            // 홈페이지: relate 배열 중 첫 번째 URL
            $homepage = null;
            if (isset($db->relate)) {
                foreach ($db->relate as $rel) {
                    $relateurl = trim((string) ($rel->relateurl ?? ''));
                    if ($relateurl) {
                        $homepage = $relateurl;
                        break;
                    }
                }
            }

            return [
                'pay'      => $this->cleanText((string) ($db->pcseguidance ?? '')),
                'content'  => $this->cleanText((string) ($db->sty           ?? '')),
                'homepage' => $homepage,
            ];
        } catch (\Exception $e) {
            Log::warning("FetchKopisEvents 상세 조회 실패 ({$mt20id}): " . $e->getMessage());
            return null;
        }
    }

    /**
     * KOPIS 날짜 파싱
     * "2025.05.01" → "2025-05-01"
     * "20250501"   → "2025-05-01"
     */
    private function parseKopisDate(string $raw): ?string
    {
        $raw = trim($raw);
        if (preg_match('/(\d{4})\.(\d{2})\.(\d{2})/', $raw, $m)) {
            return "{$m[1]}-{$m[2]}-{$m[3]}";
        }
        $digits = preg_replace('/\D/', '', $raw);
        if (strlen($digits) === 8) {
            return substr($digits, 0, 4) . '-' . substr($digits, 4, 2) . '-' . substr($digits, 6, 2);
        }
        return null;
    }

    /**
     * KOPIS 장르명 → event_gubun 매핑
     * 뮤지컬, 연극, 클래식, 오페라 등 → '공연'
     * 전시 → '전시'
     */
    private function mapGenreToGubun(string $genre): string
    {
        return match (true) {
            str_contains($genre, '전시')                                       => '전시',
            str_contains($genre, '뮤지컬')                                     => '공연',
            str_contains($genre, '연극')                                       => '공연',
            str_contains($genre, '클래식') || str_contains($genre, '서양음악') => '공연',
            str_contains($genre, '국악')   || str_contains($genre, '한국음악') => '공연',
            str_contains($genre, '오페라') || str_contains($genre, '발레')     => '공연',
            str_contains($genre, '무용')                                       => '공연',
            str_contains($genre, '서커스') || str_contains($genre, '마술')     => '공연',
            str_contains($genre, '대중음악')                                   => '공연',
            str_contains($genre, '복합')                                       => '행사',
            default                                                            => '공연',
        };
    }

    /** HTML 태그 제거 및 공백 정리 */
    private function cleanText(string $text): ?string
    {
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', trim($text));
        return $text ?: null;
    }
}
