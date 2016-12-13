<xml>
    <ToUserName><![CDATA[{!! $replyArr->ToUserName !!}]]></ToUserName>
    <FromUserName><![CDATA[{!! $replyArr->FromUserName !!}]]></FromUserName>
    <CreateTime>{!! time() !!}</CreateTime>
    <MsgType><![CDATA[text]]></MsgType>
    <Content><![CDATA[{!! $replyArr->Content !!}]]></Content>
</xml>