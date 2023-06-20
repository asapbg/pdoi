<tr>
    <td class="bg-{{ $item->statusStyle }}">{{ $item->id }}</td>
    <td>{{ $item->application_uri }}
        @if($item->parent)
            @canany(['update', 'view'], $item)
                <a class="app-f-small" href="{{ route('admin.application.view', ['item' => $item->parent->id]) }}" target="_blank">
                    <i class="fas fa-external-link-alt text-primary"
                       data-toggle="tooltip" title="{{ __('custom.to_base_application') }} {{ $item->parent->application_uri }}"></i>
                </a>
            @endcanany
        @endif
    </td>
    <td>{{ displayDate($item->created_at) }}</td>
    <td>{{ $item->responseSubject->subject_name }}</td>
    <td>
        @php($itemContent = strip_tags(html_entity_decode($item->request)))
        {{ mb_substr($itemContent, 0, 100) }}@if(strlen($itemContent) > 100){{ '...' }}@endif
    </td>
    <td>{{ $item->statusName }}</td>
    <td class="text-center">
        @canany(['update', 'view'], $item)
            <a href="{{ route( 'admin.application.view' , [$item->id]) }}"
               class="btn btn-sm btn-info"
               data-toggle="tooltip"
               title="{{ __('custom.edit') }}">
                <i class="fa fa-edit"></i>
            </a>
            <a href="/application-full-history.html"
               class="btn btn-sm btn-warning"
               data-toggle="tooltip" title=""
               data-original-title="{{ __('custom.application.full_history') }}">
                <i class="fas fa-history"></i>
            </a>
        @endcan
    </td>
</tr>
