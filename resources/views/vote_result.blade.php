@extends('layouts.app')

@section('title',$title.'结果')
@section('description',$description)

@section('body')
    <div class="weui-panel weui-panel_access">
        @if($msg!=null)
            <div class="weui-panel__hd"><i class="weui-icon-info-circle"></i>{{ $msg or ''}}</div>
        @endif
        <div class="weui-panel__hd">投票结果</div>
        <div class="weui-panel__bd">
            @for($i=1;$i<=$count;$i++)
                <a href="javascript:void(0)" class="weui-media-box weui-media-box_appmsg">
                    <div class="weui-media-box__hd">
                        <img height="100%" src="{!!$options['img'.$i] or ''!!}">
                    </div>
                    <div class="weui-media-box__bd">
                        <p class="weui-media-box__title" style="font-size: 15px">{{ $options['opt'.$i] }}</p>
                        <p class="weui-media-box__desc">{{ $stats[$i] or '0' }}票</p>
                    </div>
                </a>
            @endfor
        </div>
    </div>

    <div class="weui-panel weui-panel_access">
        <div class="weui-panel__hd">投票来源</div>
        <div class="weui-panel__bd">
            @foreach($provinces as $province =>$num)
                <a href="javascript:void(0)" class="weui-cell weui-cell_access">
                    <div class="weui-flex__item">
                        <p>{{$province!=''? $province:'未知地址'}}</p>
                    </div>
                    <div class="weui-flex__item" style="text-align: right">
                        <p>{{$num}}票</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection