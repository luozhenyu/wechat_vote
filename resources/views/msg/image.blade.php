<xml>
    <ToUserName><![CDATA[{!! $replyArr->ToUserName !!}]]></ToUserName>
    <FromUserName><![CDATA[{!! $replyArr->FromUserName !!}]]></FromUserName>
    <CreateTime>{!! time() !!}</CreateTime>
    <MsgType><![CDATA[image]]></MsgType>
    <Image>
        <MediaId><![CDATA[{!! $replyArr->MediaId !!}]]></MediaId>
    </Image>
</xml>