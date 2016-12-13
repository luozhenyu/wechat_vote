@extends('layouts.app')

@section('title',$title)
@section('description',$description)

@push('css')
<style>
    .vote-pos {
        margin: 5px;
        padding: 10px;
    }

    .vote-border {
        border-radius: 10px;
        border-style: solid;
        border-width: 1px;
        border-color: #436EEE;
    }

    #hidbg {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        background-color: #000000;
        opacity: 0.6;
        display: none;
        z-index: 3;
    }

    #hidbox {
        position: fixed;
        left: 10%;
        top: 5%;
        width: 80%;
        display: none;
        text-align: center;
        color: #BBBBBB;
        z-Index: 4;
    }

    #hidVote {
        position: fixed;
        right: 5px;
        top: 10%;
        background-color: #FFFFFF;
        opacity: 0.8;
        display: none;
        text-align: right;
        z-Index: 2;
    }

    #submitBtn {
        color: rgba(255, 255, 255, 0.6);
        background-color: #9ED99D;
    }

</style>
@endpush

@push('js')
<script>
    function show(img) {
        hidbg.style.height = document.body.scrollHeight + "px";
        hidbg.style.display = "block";
        hidimg.src = img.src;
        hidbox.style.display = "block";
    }
    function hide() {
        hidbg.style.display = "none";
        hidbox.style.display = "none";
    }
    function reCount() {
        var tot = 0;
        for (var i = 1; i <= {{$count}}; i++) {
            if (document.getElementById("s" + i).checked) {
                tot++;
            }
        }
        voteCount.innerHTML = tot;
        if (tot == 0) {
            hidVote.style.display = "none";
        } else {
            hidVote.style.display = "block";
        }

        if (tot < {{$minSelect}}|| tot > {{$maxSelect}}) {
            hidVote.style.color = '#FF0000';
            submitBtn.disabled = "disabled";
            submitBtn.style.color = "rgba(255, 255, 255, 0.6)";
            submitBtn.style.backgroundColor = "#9ED99D";
        } else {
            hidVote.style.color = '#228B22';
            submitBtn.disabled = "";
            submitBtn.style.color = "#FFFFFF";
            submitBtn.style.backgroundColor = "#1AAD19";
        }
    }
</script>
@endpush

@section('body')
    <div id="hidbg"></div>
    <div id="hidbox" onclick="hide()">
        <img id="hidimg" width="100%">
        <h2>轻触图片返回</h2>
    </div>
    <h3 id="hidVote">已选<span id="voteCount"></span>票</h3>

    <div class="weui-cells__title">投票要求</div>
    <div class="weui-cells">
        <div class="weui-cell {{$errors->has('title')?'weui-cell_warn':''}}">
            <div class="weui-cell__hd">
                <label class="weui-label">时间</label>
            </div>
            <div class="weui-cell__bd">
                <p>{{date('Y年m月d日 H:i:s',$startTime)}}</p>
                <p>至</p>
                <p>{{' '.date('Y年m月d日 H:i:s',$endTime)}}</p>
            </div>
        </div>
    </div>
    <div class="weui-cells">
        <div class="weui-cell {{$errors->has('title')?'weui-cell_warn':''}}">
            <div class="weui-cell__hd">
                <label class="weui-label">票数</label>
            </div>
            <div class="weui-cell__bd">
                @if($minSelect===$maxSelect)
                    <label class="weui-label">请投{{$maxSelect}}票</label>
                @else
                    <label class="weui-label">请投{{$minSelect.'-'.$maxSelect}}票</label>
                @endif
            </div>
        </div>
    </div>
    <form method="post">
        <div class="weui-cells__title">选择列表</div>
        <div class="weui-cells weui-cells_checkbox">
            {{ csrf_field() }}
            @for($i=1;$i<=$count;)
                <div class="weui-flex">
                    @for($j=0;$j<2;$j++,$i++)
                        @if($i<=$count)
                            <div class="weui-flex__item vote-pos vote-border">
                                <img src="{!!$options['img'.$i] or ''!!}" width="100%" onclick="show(this)">
                                <label class="weui-cell weui-check__label" for="s{{$i}}">
                                    <div class="weui-cell__hd">
                                        <input name="checkbox{{$i}}" class="weui-check" id="s{{$i}}" type="checkbox"
                                               onchange="reCount()">
                                        <i class="weui-icon-checked"></i>
                                    </div>
                                    <div class="weui-cell__bd">
                                        <p>{{$options['opt'.$i] or ''}}</p>
                                    </div>
                                </label>
                            </div>
                        @else
                            <div class="weui-flex__item vote-pos"></div>
                        @endif
                    @endfor
                </div>
            @endfor
        </div>
        <button class="weui-btn weui-btn_primary" type="submit" id="submitBtn" disabled>确认并提交</button>
    </form>
@endsection