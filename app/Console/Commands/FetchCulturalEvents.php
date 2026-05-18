<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FetchCulturalEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-cultural-events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch cultural events from Daegu API and store them in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching cultural events from Daegu API...');

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(30)
                ->withoutVerifying()
                ->get('https://dgfca.or.kr/api/daegu/cultural-events');

            if ($response->successful()) {
                $data = $response->json();
                
                if (is_array($data)) {
                    $count = 0;
                    foreach ($data as $item) {
                        if (!isset($item['event_seq'])) continue;

                        \App\Models\CulturalEvent::updateOrCreate(
                            ['event_seq' => (string) $item['event_seq']],
                            [
                                'subject' => $item['subject'] ?? '제목 없음',
                                'event_gubun' => $item['event_gubun'] ?? null,
                                'start_date' => $item['start_date'] ?? date('Y-m-d'),
                                'end_date' => $item['end_date'] ?? date('Y-m-d'),
                                'place' => $item['place'] ?? null,
                                'pay' => $item['pay'] ?? null,
                                'content' => $item['content'] ?? null,
                                'homepage' => $item['homepage'] ?? null,
                            ]
                        );
                        $count++;
                    }
                    $this->info("Successfully synced {$count} cultural events.");
                } else {
                    $this->error('API response is not an array.');
                }
            } else {
                $this->error('Failed to fetch from API. Status: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->error('Error occurred: ' . $e->getMessage());
            \Log::error('FetchCulturalEvents Command Error: ' . $e->getMessage());
        }
    }
}
