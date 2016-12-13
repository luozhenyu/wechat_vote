@extends('layouts.app')


@section('body')
    <div class="icon-box" style="text-align: center">
        <i class="weui-icon-info weui-icon_msg"></i>
        <div class="icon-box__ctn">
            <br>
            <p class="icon-box__desc">{!!$notice!!}</p>
        </div>
        <div style="margin-top:40px;">
            <a href="http://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=MzAwMzc1ODI3OA==#wechat_redirect"
               class="weui-btn weui-btn_plain-primary">点击头像关注我们</a>
        </div>
    </div>

    <div class="weui-footer weui-footer_fixed-bottom">
        <p class="weui-footer__text">知心助航 zizhu_buaa</p>
    </div>
@endsection