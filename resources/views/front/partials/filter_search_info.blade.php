@if(isset($filter) && count($filter))
    @foreach($filter as $keyName => $search)
        @if(isset($search['value']) && !empty($search['value']))
            <span class="badge rounded-pill text-bg-light ms-2 fw-normal" style="font-size: 12px;">
                @php($name = '')
                @php($value = '')
                @switch($search['type'])
                    @case('checkbox')
                        @php($name = $search['label'])
                    @break
                    @case('select')
                    @case('subjects')
                        @php($name = $search['placeholder'])
                        @foreach($search['options'] as $op)
                            @if($op['value'] == $search['value'])
                                @php($value = $op['name'])
                            @endif
                        @endforeach
                    @break
                    @default()
                        @php($name = $search['placeholder'])
                        @php($value = $search['value'])
                @endswitch
                {{ $name }} @if(!empty($value))({{ $value }})@endif
                <i class="fas fa-remove clear-search-param" role="button" data-field="{{ $keyName }}" data-type="{{ $search['type'] }}"></i>
            </span>
        @endif
    @endforeach
@endif

@push('scripts')
    <script type="text/javascript"  nonce="2726c7f26c">
        $(document).ready(function (){
            $('.clear-search-param').on('click', function (){
                // if($(this).data('type'))
                $('[name="'+ $(this).data('field') +'"]').val('');
                $('[name="'+ $(this).data('field') +'"]').prop('checked', false);
                $('.form-filter').submit();
            });
        });
    </script>
@endpush
