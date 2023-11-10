<table class="table table-hover table-bordered" width="100%" cellspacing="0">
    <thead>
    <tr>
        <th>ID</th>
        <th>{{__('validation.attributes.username')}}</th>
        <th>{{__('validation.attributes.first_name')}}</th>
        <th>Презиме</th>
        <th>Фамилия</th>
        <th>Склад</th>
        <th>Email</th>
        <th>Телефон</th>
        <th>Описание</th>
        <th>Статус активност</th>
        <th>Email потвърден на</th>
        <th>Парола</th>
        <th>Парола сменена на</th>
        <th>Токен</th>
        <th>Последен вход на</th>
        <th>{{__('custom.active_m')}}</th>
        <th>Роля</th>
        <th>Създаден на</th>
        <th>Обновен на</th>
        <th>Изтрит на</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($users) && $users->count() > 0)
        @foreach($users as $user)
            <tr>
                <td>{{$user->id}}</td>
                <td>{{$user->username}}</td>
                <td>{{$user->first_name}}</td>
                <td>{{$user->middle_name}}</td>
                <td>{{$user->last_name}}</td>
                <td>{{$user->location_number}}</td>
                <td>{{$user->email}}</td>
                <td>{{$user->phone}}</td>
                <td>{{$user->description}}</td>
                <td>{{$user->status}}</td>
                <td>{{$user->email_verified_at}}</td>
                <td>{{$user->password}}</td>
                <td>{{$user->pass_last_change}}</td>
                <td>{{$user->remember_token}}</td>
                <td>{{$user->last_login_at}}</td>
                <td>{{ ($user->active) ? 1 : 0 }}</td>
                <td>
                    {{ implode(', ',$user->roles->pluck('display_name')->toArray()) }}
                </td>
                <td>{{$user->created_at}}</td>
                <td>{{$user->updated_at}}</td>
                <td>{{$user->deleted_at}}</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
