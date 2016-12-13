<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;
use Cache;

class AccessToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accessToken';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get access_token';

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
        $appID = env('WECHAT_AppID');
        $appSecret = env('WECHAT_AppSecret');
        $res = file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appID . "&secret=" . $appSecret);
        $res = json_decode($res);

        Cache::put('wechat_access_token', $res->access_token, $res->expires_in);

        Storage::append('cron.log',date("Y-m-d H:i:s").' accessToken refreshed');
    }
}
