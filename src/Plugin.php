<?php

namespace PMC;

use Illuminate\Support\LazyCollection;
use PMC\Concerns\Instanceable;

class Plugin
{
    use Instanceable;

    protected Logger $log;

    public function logger(): Logger
    {
        return $this->log ??= new Logger();
    }

    public function getDuplicateMetaRowCounts(\wpdb $wpdb)
    {
        global $wpdb;

        $startTime = microtime(true);

        $duplicatedPostIdsWithCount = LazyCollection::make($wpdb->get_results('SELECT post_id,meta_key,meta_value,COUNT(post_id)AS count FROM wp_owc0h41lg8_postmeta GROUP BY meta_value,post_id,meta_key HAVING(count>1) ORDER BY COUNT(post_id)DESC'));
        $rows = $duplicatedPostIdsWithCount->map(function ($row) {
            return [
                'post_id' => (int) $row->post_id,
                'count' => (int) $row->count,
            ];
        });

        $endTime = microtime(true);

        \ray($endTime - $startTime)->blue();

        \update_option('pmc_multiple_meta_rows', $rows->toArray());

        return $rows;
    }

    public function calculateDuplicateMeta(\wpdb $wpdb)
    {
        $postsWithDuplicateMeta = $this->getDuplicateMetaRowCounts($wpdb);
        $total = $postsWithDuplicateMeta->sum('count');
        $totalDuplicates = $total - $postsWithDuplicateMeta->count();

        \update_option('pmc_duplicate_meta_count', $totalDuplicates);

        return $totalDuplicates;
    }

    public function run(): void
    {
        add_action('plugins_loaded', function () {
            require_once PMC_DIR_PATH.'/inc/options.php';
        }, 10);
    }
}
