@extends('layouts.app')

@section('title','创建投票')
@section('description','添加内容以创建一个投票')

@push('css')
@endpush

@push('js')
<script src="//cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>
<script src="/laydate/laydate.js"></script>
<script>
    $(function () {
        $(document).on({
            dragleave: function (e) {
                e.preventDefault();
            },
            drop: function (e) {
                e.preventDefault();
            },
            dragenter: function (e) {
                e.preventDefault();
            },
            dragover: function (e) {
                e.preventDefault();
            }
        });

        for (var i = 1; i <= {{$count}}; i++) {
            document.getElementById('cell' + i).addEventListener("drop", function (e) {
                e.preventDefault();
                var file = e.dataTransfer.files[0];
                if (file == null || file.type.indexOf('image') === -1) {
                    alert("不支持的格式");
                    return false;
                }
                if (file.size / 1024 > 20) {
                    alert("图片大小不能超过20K.");
                    return false;
                }

                var cur = e.currentTarget.id.substr(4);
                var fileName = file.name;
                fileName = fileName.substr(0, fileName.indexOf("."));
                $('#opt' + cur).val(fileName);

                var reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = function (e) {
                    $('#img' + cur).val(e.target.result);
                };

            }, false);
        }
    });
</script>
@endpush

@section('body')
    <form method="post" action="{{url('create')}}">
        {{ csrf_field() }}
        <input type="hidden" name="count" value="{{$count}}">

        <div class="weui-cells__title">新投票</div>
        <div class="weui-cells">
            <div class="weui-cell {{$errors->has('title')?'weui-cell_warn':''}}">
                <div class="weui-cell__hd">
                    <label class="weui-label">标题</label>
                </div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="input" name="title" placeholder="请输入投票标题（40字内）" maxlength="40"
                           value="{{ old('title') }}">
                </div>
            </div>
            <div class="weui-cell {{$errors->has('description')?'weui-cell_warn':''}}">
                <div class="weui-cell__hd">
                    <label class="weui-label">详细描述</label>
                </div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="input" name="description" placeholder="请输入描述内容（100字内）"
                           maxlength="100" value="{{ old('description') }}">
                </div>
            </div>
        </div>

        <div class="weui-cells__title">投票要求</div>
        <div class="weui-cells">
            <div class="weui-cell {{$errors->has('maxSelect')?'weui-cell_warn':''}}">
                <div class="weui-cell__hd">
                    <label class="weui-label">开始时间</label>
                </div>
                <div class="weui-cell__bd">
                    <li class="laydate-icon" id="start" style="width:200px;list-style: none"></li>
                    <input id="start_hidden" name="startTime" type="hidden">
                </div>
            </div>
            <div class="weui-cell {{$errors->has('deadline')?'weui-cell_warn':''}}">
                <div class="weui-cell__hd">
                    <label class="weui-label">截止时间</label>
                </div>
                <div class="weui-cell__bd">
                    <li class="laydate-icon" id="end" style="width:200px;list-style: none"></li>
                    <input id="end_hidden" name="endTime" type="hidden">
                </div>
            </div>

            <script>
                function mktime(datetime) {
                    var res = datetime.split(' ');
                    var d = res[0].split('/');
                    var t = res[1].split(':');
                    var dt = new Date();
                    dt.setFullYear(d[0]);
                    dt.setMonth(d[1] - 1);
                    dt.setDate(d[2]);
                    dt.setHours(t[0]);
                    dt.setMinutes(t[1]);
                    dt.setSeconds(t[2]);
                    return Math.round(dt.valueOf() / 1000);
                }

                var start = {
                    elem: '#start',
                    format: 'YYYY/MM/DD hh:mm:ss',
                    min: laydate.now(),
                    max: '2099-06-16 23:59:59',
                    istime: true,
                    istoday: false,
                    choose: function (datas) {
                        end.min = datas;
                        end.start = datas;
                        start_hidden.value = mktime(datas);
                    }
                };
                var end = {
                    elem: '#end',
                    format: 'YYYY/MM/DD hh:mm:ss',
                    min: laydate.now(),
                    max: '2099-06-16 23:59:59',
                    istime: true,
                    istoday: false,
                    choose: function (datas) {
                        start.max = datas;
                        end_hidden.value = mktime(datas);
                    }
                };
                laydate(start);
                laydate(end);
            </script>

            <div class="weui-cell {{$errors->has('minSelect')?'weui-cell_warn':''}}">
                <div class="weui-cell__hd">
                    <label class="weui-label">最少票数</label>
                </div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" name="minSelect" placeholder="请输入数值"
                           value="{{ old('minSelect') }}">
                </div>
            </div>
            <div class="weui-cell {{$errors->has('maxSelect')?'weui-cell_warn':''}}">
                <div class="weui-cell__hd">
                    <label class="weui-label">最多票数</label>
                </div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" name="maxSelect" placeholder="请输入数值"
                           maxlength="100" value="{{ old('maxSelect') }}">
                </div>
            </div>
        </div>

        <div class="weui-cells__title">选项列表</div>
        <div class="weui-cells">
            @for ($i = 1; $i <= $count; $i++)
                <div class="weui-cell {{$errors->has('opt'. $i)||$errors->has('img'. $i)?'weui-cell_warn':''}}"
                     id="cell{{$i}}">
                    <div class="weui-cell__hd">
                        <label class="weui-label">选项{{ $i }}</label>
                    </div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" type="input" name="opt{{ $i }}" id="opt{{ $i }}"
                               placeholder="请输入内容（15字内）"
                               maxlength="15" value="{{ old('opt'. $i) }}">
                        <input class="weui-input" type="input" name="img{{ $i }}" id="img{{ $i }}"
                               placeholder="可使用网址或base64格式"
                               maxlength="200" value="{{ old('img'. $i) }}">
                    </div>
                </div>
            @endfor
        </div>

        <div class="weui-footer">
            <button class="weui-btn weui-btn_primary" type="submit">立即创建</button>
        </div>
    </form>
@endsection