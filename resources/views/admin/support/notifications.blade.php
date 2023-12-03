@extends('layouts.admin')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="card">
                <form method="GET">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 d-flex flex-row">
                                <input type="text" name="application" autocomplete="off"
                                       class="form-control form-control-sm"
                                       placeholder="Заявление"
                                       value="{{ old('application', $filter['application'] ?? '') }}" >
                            </div>
                            <div class="col-md-4 d-flex flex-row">
                                <input type="text" name="email" autocomplete="off"
                                       class="form-control form-control-sm"
                                       placeholder="E-mail"
                                       value="{{ old('email', $filter['email'] ?? '') }}" >
                            </div>
                            <div class="col-md-4 d-flex flex-row">
                                <input type="checkbox" name="not_send" class="custom-checkbox mr-2"
                                       value="1" @if(old('not_send', 0 ) == 1) checked @endif> Неизпратени
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-success mt-2">Търсене</button>
                    </div>

                </form>
            </div>

            <div class="card">
                <div class="card-body table-responsive">

                    <table class="table table-sm table-hover table-bordered" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Метод (известие)</th>
                            <th>Изпратено</th>
                            <th>Опити за изпращане</th>
                            <th>Тип на известието</th>
                            <th>Получател</th>
                            <th>СЕОС (egov_message_id)</th>
                            <th>ССЕВ ID</th>
                            <th>Създадено</th>
                            <th>Обновено</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($items) && $items->count() > 0)
                            @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ __('custom.rzs.delivery_by.'.\App\Enums\PdoiSubjectDeliveryMethodsEnum::keyByValue($item->type_channel)) }}</td>
                                    <td><i class="fas @if($item->is_send) fa-check text-success @else fa-minus text-danger @endif"></i></td>
                                    <td>{{ $item->cnt_send }}</td>
                                    <td>{{ $item->type }}</td>
                                    <td>
                                        @if($item->notifiable_type == 'App\Models\PdoiResponseSubject')
                                            <a class="text-decoration-underline" href="{{ route('admin.rzs.view', $item->notifiable_id) }}" target="_blank">ЗС</a>
                                        @elseif($item->notifiable_type == 'App\Models\User')
                                            <a class="text-decoration-underline" href="{{ route('admin.users.edit', $item->notifiable_id) }}" target="_blank">Потребител</a>
                                        @else
                                            {{ $item->notifiable_type }} | {{ $item->notifiable_id }}
                                        @endif
                                    </td>
                                    <td>{{ $item->egov_message_id ?? '---' }}</td>
                                    <td>{{ $item->msg_integration_id ?? '---' }}</td>
                                    <td>{{ displayDateTime($item->created_at) }}</td>
                                    <td>{{ displayDateTime($item->updated_at) }}</td>
                                    <td><a href="{{ route('admin.support.notifications.view', ['id' => $item->id]) }}" target="_blank"><i class="fas fa-eye text-warning"></i></a></td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>

                <div class="card-footer mt-2">
                    @if(isset($items) && $items->count() > 0)
                        {{ $items->appends(request()->query())->links() }}
                    @endif
                </div>
            </div>

        </div>
    </section>

@endsection


