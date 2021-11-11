<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Models\Redirect;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ChangeNewsSlug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change_news_slug {oldSlug} {newSlug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $oldSlug = $this->argument('oldSlug');
        $newSlug = $this->argument('newSlug');

        if ($oldSlug === $newSlug) {
            $this->error("slugs are identical");
            return 1;
        }
        $check_exist = Redirect::where('old_slug', route('news_item', ['slug' => $oldSlug], false))
            ->where('new_slug', route('news_item', ['slug' => $newSlug], false))
            ->exists();
        if ($check_exist) {
            $this->error('this move have already been');
            return 1;
        }
        $news_item = News::where('slug', $oldSlug)->first();
        if ($news_item === null) {
            $this->error("a news_item with such slug is not exist");
            return 1;
        }
        DB::transaction(function () use ($news_item, $newSlug) {
            Redirect::where('old_slug', route('news_item', ['slug' => $newSlug], false))->delete();
            $news_item->slug = $newSlug;
            $news_item->save();
        });

        return 0;
    }
}
