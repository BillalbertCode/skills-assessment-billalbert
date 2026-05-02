<?php

namespace Dustov\Quotes\Commands;

use Dustov\Quotes\Exceptions\RateLimitExceeded;
use Dustov\Quotes\QuotesManager;
use Dustov\Quotes\Services\QuoteApiClient;
use Illuminate\Console\Command;

class BatchImportCommand extends Command
{
    protected $signature = 'quotes:batch-import {count}';
    protected $description = 'import quotes in a resilient way';

    public function handle(QuoteApiClient $api, QuotesManager $manager)
    {
        $target = (int) $this->argument('count');
        $imported = 0;
        $skip = 0;

        $windowTime = config('quotes.rate_limit.window_in_seconds');

        $bar = $this->output->createProgressBar($target);
        $bar->start();

        while ($imported < $target) {
            try {
                $limit = min(30, $target - $imported);
                $batch = $api->fetchMany($limit, $skip);

                if (empty($batch['quotes'])) {
                    break;
                }

                // manager add Batch
                $newlyAdded = $manager->addBatch($batch['quotes']);

                //update counters
                $imported +=  $newlyAdded;
                $skip += count($batch['quotes']);

                $bar->advance($newlyAdded);

                // If we reach the total API limit, we stop even if we don't reach the target
                if ($skip >= $batch['total']) break;
            } catch (RateLimitExceeded $e) {
                $this->warn("\nLimit reached. Waiting to resume... {$windowTime} seconds");
                sleep($windowTime);
            } catch (\Exception $e) {
                $this->error("\nError:" . $e->getMessage());
                break;
            }
        }
        $bar->finish();
        $this->info('import complete!');
    }
}
