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

    public function getDuplicateMetaRowCounts()
    {
        global $wpdb;

        $duplicatedPostIdsWithCount = LazyCollection::make($wpdb->get_results("SELECT post_id,meta_key,count(*)FROM {$wpdb->postmeta} GROUP BY post_id,meta_key HAVING count(*)>1 ORDER BY COUNT(*)DESC;"));
        $rows = $duplicatedPostIdsWithCount->map(function ($row) {
            return [
                'post_id' => (int) $row->post_id,
                'meta_key' => $row->meta_key,
                'count' => (int) $row->{'count(*)'},
            ];
        });

        \update_option('pmc_multiple_meta_rows', $rows->toArray());

        return $rows;
    }

    public function calculateDuplicateMeta()
    {
        global $wpdb;

        $postsWithDuplicateMeta = $this->getDuplicateMetaRowCounts($wpdb);
        $total = $postsWithDuplicateMeta->sum('count');
        $totalDuplicates = $total - $postsWithDuplicateMeta->count();

        \update_option('pmc_duplicate_meta_count', $totalDuplicates);

        return $totalDuplicates;
    }

    public function getDuplicateRows(int $postId, string $metaKey)
    {
        global $wpdb;

        return LazyCollection::make($wpdb->get_results("SELECT p1.meta_id,p1.post_id,p1.meta_key,p2.meta_id,p2.post_id,p2.meta_key FROM {$wpdb->postmeta} p1 JOIN {$wpdb->postmeta} p2 ON p1.meta_id=p2.meta_id AND p1.meta_key=p2.meta_key AND p1.post_id={$postId} AND p1.meta_value=p2.meta_value AND p1.meta_key='{$metaKey}' AND p1.post_id=p2.post_id ORDER BY p1.meta_id ASC;"))->slice(1);
    }

    public function deleteDuplicateRows(LazyCollection $rows)
    {
        global $wpdb;

        $rows->chunk(1000)->each(function ($rows) use ($wpdb) {
            $ids = $rows->pluck('meta_id')->toArray();

            $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_id IN (".implode(',', $ids).')');
        });
    }

    public function run(): void
    {
        add_action('plugins_loaded', function () {
            require_once PMC_DIR_PATH.'/inc/options.php';
        }, 10);
    }
}
