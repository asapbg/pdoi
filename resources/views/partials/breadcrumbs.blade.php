@if (isset($breadcrumbs) && !request()->routeIs('admin.home'))
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1> {{ capitalize($breadcrumbs['heading']) }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    @foreach($breadcrumbs['links'] as $key => $link)
                        @if($key < $breadcrumbs['links_count'])
                            <li class="breadcrumb-item">
                                <a href="{{ $link['url'] }}">{{ capitalize($link['name']) }}</a>
                            </li>
                        @else
                            <li class="breadcrumb-item active">
                                {{ capitalize($link['name']) }}
                            </li>
                        @endif
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</section>
@endif
