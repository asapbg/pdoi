@if(isset($applicationsInfo) && sizeof($applicationsInfo))
    <div class="card card-light mb-4 w-50 m-auto">
        <div class="card-header app-card-header py-2">
            <h4 class="fs-5 m-0">&nbsp;</h4>
        </div>
        <div class="card-body">
            <div class="text-center">
                <div class="d-flex justify-content-center align-items-center">
                    <i class="text-success fa-solid fa-circle-check fs-3 me-2"></i>
                    <p class="m-0 fs-4">{{ __('front.application.apply.success') }}</p>
                </div>
                @foreach($applicationsInfo as $application)
                    @if(!$loop->first)<hr>@endif
                    <div class="m-auto lh-1 mt-md-5 mt-3">
                        <p><span class="fw-semibold">{{ __('custom.reg_number') }}: </span> {{ $application['reg_number'] }}</p>
                        <p><span class="fw-semibold">{{ trans_choice('custom.pdoi_response_subjects', 1) }}: </span> {{ $application['response_subject'] }}</p>
                        <p><span class="fw-semibold">{{ __('custom.status') }}: </span> {{ $application['status'] }}</p>
                        <p><span class="fw-semibold">{{ __('custom.status_date') }}: </span> {{ $application['status_date'] }}</p>
                        <p><span class="fw-semibold">{{ __('custom.end_date') }}: </span> {{ $application['response_end_time'] }}</p>
                    </div>
                @endforeach
                <button class="btn btn-primary mt-3" href="" onclick="window.print();return false;"><i class="text-white fa-solid fa-print me-1"></i>{{ __('custom.print') }}</button>
            </div>
        </div>
    </div>
@else
    <p class="text-danger">{{ __('custom.system_error') }}</p>
@endif
