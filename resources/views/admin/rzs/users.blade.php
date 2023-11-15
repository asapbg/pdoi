<div class="row mb-4">
    <h5 class="bg-primary py-1 px-2 mb-4">{{ trans_choice('custom.users', 2) }}</h5>
    <table class="table table-striped">
        <thead>
            <tr>
                <td>{{ __('custom.name') }}</td>
                <td>{{ __('custom.email') }}</td>
                <td>{{ __('custom.phone') }}</td>
            </tr>
        </thead>
        <tbody>
            @if(isset($users) && $users->count())
                @foreach($users as $user)
                    <tr>
                        <td>
                            <a class="text-decoration-underline" target="_blank" href="{{ route('admin.users.edit', $user) }}">{{ $user->fullName() }}</a>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3">
                        Няма потребители
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

</div>
