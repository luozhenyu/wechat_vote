<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

include_once "encodingAES/wxBizMsgCrypt.php";

class WechatController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function getMethod(Request $request)
    {
        return $request->input('echostr');
    }

    public function postMsg(Request $request)
    {
        $pc = new \WXBizMsgCrypt(env('WECHAT_Token'), env('WECHAT_EncodingAESKey'), env('WECHAT_AppID'));

        $encryptedData = $request->getContent();
        $msg_sign = $request->input('msg_signature');
        $timeStamp = $request->input('timestamp');
        $nonce = $request->input('nonce');

        $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $encryptedData, $webData);

        if ($errCode !== 0) {
            return $errCode;
        }

        libxml_use_internal_errors(true);
        libxml_disable_entity_loader(true);
        $postObj = simplexml_load_string($webData, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($postObj === false) {
            return "Invalid XML!";
        }

        $str = function () use (&$postObj) {
            switch ($postObj->MsgType) {
                case 'text':
                    return $this->dealText($postObj);
                case 'event':
                    return $this->dealEvent($postObj);
                default:
                    return $this->defaultText($postObj);
            }
        };
        $errCode = $pc->encryptMsg($str(), $timeStamp, $nonce, $encryptMsg);

        if ($errCode !== 0) {
            return $errCode;
        }
        return $encryptMsg;
    }

    private function dealText($postObj)
    {
        $replyArr = new \stdClass();
        $replyArr->ToUserName = $postObj->FromUserName;
        $replyArr->FromUserName = $postObj->ToUserName;
        $keyword = trim($postObj->Content);
        $replyArr->Content = '';
        return view('msg/text', ['replyArr' => $replyArr]);
    }

    private function dealEvent($postObj)
    {
        $replyArr = new \stdClass();
        $replyArr->ToUserName = $postObj->FromUserName;
        $replyArr->FromUserName = $postObj->ToUserName;
        switch ($postObj->Event) {
            case 'subscribe':
                $replyArr->Content =
                    '';
                break;
            default:
                $replyArr->Content = '';
                break;
        }
        return view('msg/text', ['replyArr' => $replyArr]);
    }

    private function defaultText($postObj)
    {
        $replyArr = new \stdClass();
        $replyArr->ToUserName = $postObj->FromUserName;
        $replyArr->FromUserName = $postObj->ToUserName;

        $replyArr->Content = '';
        return view('msg/text', ['replyArr' => $replyArr]);
    }
}
