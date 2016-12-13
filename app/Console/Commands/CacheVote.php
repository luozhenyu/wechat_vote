<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\VoteResult;
use App\User;

class CacheVote extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cacheVote {voteid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache vote results into redis';

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
     * @return mixed
     */
    public function handle()
    {
        $voteid = $this->argument('voteid');
        Redis::del('voteRet-' . $voteid);
        Redis::del('province-' . $voteid);

        $voteResults = VoteResult::where('voteid', $voteid)->get();

        foreach ($voteResults as $voteResult) {
            $openid = $voteResult->openid;

            $user = User::find($openid);

            if ($user !== null) {
                Redis::hincrby('province-' . $voteid, $user->province, 1);
            }

            $selected = json_decode($voteResult->selected, true);
            foreach ($selected as $opt) {
                Redis::hincrby('voteRet-' . $voteid, $opt, 1);
            }
        }
    }
}
