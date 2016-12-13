<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Cache;
use Validator;
use Session;
use App\Vote;
use App\User;
use App\VoteResult;

class VoteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function createVote(Request $request)
    {
        if ($request->input('code') != env('WECHAT_Code')) {
            abort(404);
        }
        $count = $request->input('count');
        if (!is_numeric($count) || $count < 2 || $count > 99) {
            return redirect('create?count=3');
        }
        return view('vote_create', ['count' => $count]);
    }

    public function saveVote(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:40',
            'description' => 'required|max:100',
            'minSelect' => 'required|numeric',
            'maxSelect' => 'required|numeric',
            'startTime' => 'required|numeric',
            'endTime' => 'required|numeric',
            'count' => 'required|numeric|min:1|max:99'
        ]);
        $title = $request->input('title');
        $description = $request->input('description');
        $count = $request->input('count');

        $minSelect = $request->input('minSelect');
        $maxSelect = $request->input('maxSelect');

        $startTime = $request->input('startTime');
        $endTime = $request->input('endTime');

        $rules = [
            'minSelect' => 'numeric|min:1|max:' . $count,
            'maxSelect' => 'numeric|min:' . $minSelect . '|max:' . $count
        ];
        $options = [];
        for ($i = 1; $i <= $count; $i++) {
            $opt = 'opt' . $i;
            $img = 'img' . $i;
            $rules += [$opt => 'required|max:35'];
            $rules += [$img => 'required'];
            $options += [$opt => $request->input($opt)];
            $options += [$img => $request->input($img)];
        }
        $this->validate($request, $rules);

        $vote = new Vote;
        $vote->title = $title;
        $vote->description = $description;
        $vote->count = $count;
        $vote->minSelect = $minSelect;
        $vote->maxSelect = $maxSelect;
        $vote->startTime = $startTime;
        $vote->endTime = $endTime;
        $vote->options = json_encode($options);
        $vote->save();

        return $this->getVoteUrl($vote->voteid);
    }

    public function getVote(Request $request, $voteid)
    {
        $vote = Vote::findOrFail($voteid);
        $curTime = time();
        if ($curTime < $vote->startTime) {
            return $this->showVoteResults($vote, '投票就快就开始啦，请等待一下吧');
        }

        if ($curTime > $vote->endTime) {
            return $this->showVoteResults($vote, '投票已经结束，非常感谢各位的参与！');
        }

        //session 有canVote快速验证
        if ($request->session()->get('canVote-' . $voteid)) {
            return view('vote', [
                'title' => $vote->title,
                'description' => $vote->description,
                'minSelect' => $vote->minSelect,
                'maxSelect' => $vote->maxSelect,
                'startTime' => $vote->startTime,
                'endTime' => $vote->endTime,
                'count' => $vote->count,
                'options' => json_decode($vote->options, true),
            ]);
        }
        $validator = Validator::make($request->all(), [
            'state' => 'required',
            'code' => 'required',
        ]);
        if ($validator->fails()) {
            return view('notice', [
                'notice' => '<p>请使用微信客户端访问</p><p>如您已经使用了微信客户端</p><p>请<a href="' . $this->getVoteUrl($voteid) . '">点击立即投票</a></p>'
            ]);
        }
        //获得openid及其他基本信息
        $code = $request->input('code');
        $retVal = json_decode(self::postRequest('https://api.weixin.qq.com/sns/oauth2/access_token', [
            'appid' => env('WECHAT_AppID'),
            'secret' => env('WECHAT_AppSecret'),
            'code' => $code,
            'grant_type' => 'authorization_code'
        ]), true);
        if (isset($retVal['errcode'])) {
            return view('notice', [
                'notice' => '<p>请使用微信客户端访问</p><p>如您已经使用了微信客户端</p><p>请<a href="' . $this->getVoteUrl($voteid) . '">点击立即投票</a></p>'
            ]);
        }
        $retVal = json_decode(self::postRequest('https://api.weixin.qq.com/cgi-bin/user/info', [
            'access_token' => Cache::get('wechat_access_token'),
            'openid' => $retVal['openid'],
            'lang' => 'zh_CN'
        ]), true);
        if (isset($retVal['errcode'])) {
            return $this->showVoteResults($vote);
        }
        if (!$retVal['subscribe']) {
            return view('notice', [
                'notice' => "请先关注我们的公众号"
            ]);
        }
        $openid = $retVal['openid'];
        //保存用户信息
        $user = User::find($openid);
        if (is_null($user)) {
            $user = new User;
        }
        $user->openid = $openid;
        $user->nickname = $retVal['nickname'];
        $user->sex = $retVal['sex'];
        $user->language = $retVal['language'];
        $user->city = $retVal['city'];
        $user->province = $retVal['province'];
        $user->country = $retVal['country'];
        $user->subscribe_time = $retVal['subscribe_time'];
        $user->save();

        //将openid存入session
        $request->session()->put('openid', $openid);
        $request->session()->put('province', $retVal['province']);

        //检测是否投过票
        $voteRet = VoteResult::where('openid', $openid)->where('voteid', $voteid)->first();
        if (!is_null($voteRet)) {
            return $this->showVoteResults($vote, '亲，您已投过票了噢。');
        }
        $request->session()->put('canVote-' . $voteid, 'true');

        return view('vote', [
            'title' => $vote->title,
            'description' => $vote->description,
            'minSelect' => $vote->minSelect,
            'maxSelect' => $vote->maxSelect,
            'startTime' => $vote->startTime,
            'endTime' => $vote->endTime,
            'count' => $vote->count,
            'options' => json_decode($vote->options, true),
        ]);
    }

    public function postVote(Request $request, $voteid)
    {
        $vote = Vote::findOrFail($voteid);
        $curTime = time();
        if ($curTime < $vote->startTime || $curTime > $vote->endTime) {
            return view('notice', [
                'notice' => "已过投票时间"
            ]);
        }

        //检查canVote
        if (!$request->session()->get('canVote-' . $voteid)) {
            return $this->showVoteResults($vote);
        }
        $openid = $request->session()->get('openid');
        $count = $vote->count;
        $minSelect = $vote->minSelect;
        $maxSelect = $vote->maxSelect;
        $startTime = $vote->startTime;
        $endTime = $vote->endTime;
        $selected = [];
        for ($i = 1; $i <= $count; $i++) {
            if ($request->input('checkbox' . $i) === 'on') {
                $selected[] = $i;
            }
        }
        if (count($selected) < $minSelect || count($selected) > $maxSelect) {
            return redirect()->back();
        }

        //存储得票
        $voteRet = new VoteResult;
        $voteRet->openid = $openid;
        $voteRet->voteid = $voteid;
        $voteRet->selected = json_encode($selected);
        $voteRet->save();
        $request->session()->forget('canVote-' . $voteid);
        //缓存投票

        foreach ($selected as $key => $value) {
            Redis::hincrby('voteRet-' . $voteid, $value, 1);
        }
        //缓存省份
        $province = $request->session()->get('province');
        Redis::hincrby('province-' . $voteid, $province, 1);
        return $this->showVoteResults($vote, '恭喜，投票成功！');
    }

    private function showVoteResults($vote, $msg = null)
    {
        $voteid = $vote->voteid;
        $stats = Redis::hgetall('voteRet-' . $voteid);
        $provinces = Redis::hgetall('province-' . $voteid);
        arsort($provinces, SORT_NUMERIC);
        return view('vote_result', [
            'voteid' => $voteid,
            'title' => $vote->title,
            'count' => $vote->count,
            'description' => $vote->description,
            'options' => json_decode($vote->options, true),
            'stats' => $stats,
            'provinces' => $provinces,
            'msg' => $msg,
        ]);
    }

    private static function postRequest($url, $data)
    {
        $postData = http_build_query($data);
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postData
            )
        );
        $context = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);
        return $result;
    }

    public function getVoteUrl($voteid)
    {
        $appid = env('WECHAT_AppID');
        $realUrl = 'https://vote.luozy.cn/vote/' . $voteid;
        $redirectUrl = urlencode($realUrl);
        $str = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . $redirectUrl . '&response_type=code&scope=snsapi_userinfo&state=ok#wechat_redirect';
        return $str;
    }

}
