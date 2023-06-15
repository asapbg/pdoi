<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ trans_choice('custom.applications', 1) }} - {{ $application->application_uri }}</title>
</head>
<body style="text-align: center;font-size: 34px; padding-left: 50px; padding-right: 50px;">
<div style="font-size: 22px;">
    <table>
        <tr>
            <td colspan=""><img src="{{ asset('img/coat_arms.png') }}" width="220" height="auto"></td>
            <td colspan="4" style="font-size: 30px; text-align: left;"><strong>{{ mb_strtoupper(__('custom.access_to_public_info')) }}</strong></td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td style="text-align: left;font-size: 26px;"><strong>{{ mb_strtoupper(__('custom.to')) }}<br> {{ $application->responseSubject->subject_name }}</strong></td>
        </tr>
        <tr><td colspan="5">&nbsp;</td></tr>
        <tr><td colspan="5">&nbsp;</td></tr>
        <tr><td colspan="5">&nbsp;</td></tr>
        <tr><td colspan="5" style="text-align: center;font-size: 34px;"><strong>{{ mb_strtoupper(trans_choice('custom.applications', 1)) }}</strong></td></tr>
        <tr><td colspan="5" style="text-align: center;">{{ __('custom.for') }} {{ __('custom.access_to_public_info') }}</td></tr>
        <tr><td colspan="5" style="text-align: center;">{{ __('custom.application_uri') }} {{ $application->application_uri }}</td></tr>
        <tr><td colspan="5">&nbsp;</td></tr>
        <tr><td colspan="5">&nbsp;</td></tr>
        <tr>
            <td style="text-align: left;">{{ ucfirst(__('custom.from')) }} <strong>{{ $application->applicant->names }}</strong></td>
            <td colspan="4">&nbsp;</td>
        </tr>
        <tr><td colspan="5">&nbsp;</td></tr>
        <tr>
            <td colspan="5" style="text-align: justify;">
                {{ __('custom.phone') }}: @if(!empty($application->phone)){{ $application->phone }}@else{{ '---' }}@endif,
                {{ __('custom.email') }}: @if(!empty($application->email)){{ $application->email }}@else{{ '---' }}@endif
            </td>
        </tr>
        <tr><td colspan="5">&nbsp;</td></tr>
        <tr>
            <td colspan="5" style="text-align: justify;">
                {{ __('custom.address_for_contact') }}: {{ lcfirst(trans_choice('custom.area', 1)) }} {{ $application->area->ime }},
                {{ lcfirst(trans_choice('custom.municipality', 1)) }} {{ $application->municipality->ime }},
                {{ lcfirst(trans_choice('custom.settlement', 1)) }} {{ $application->settlement->ime }},
                {{ lcfirst(__('custom.address')) }} {{ $application->address.(!empty($application->address_second) ? ', ' : '').$application->address_second }}.
            </td>
        </tr>
        <tr><td colspan="5">&nbsp;</td></tr>
        <tr><td colspan="5">&nbsp;</td></tr>
        <tr>
            <td colspan="5" style="text-align: justify;">
                {!! html_entity_decode($application->request) !!}
            </td>
        </tr>
        <tr><td colspan="5">&nbsp;</td></tr>
        <tr>
            <td colspan="5" style="text-align: left;">
                {{ __('custom.date_apply') }}: {{ displayDate($application->created_at) }} г.
            </td>
        </tr>
        <tr>
            <td colspan="5" style="text-align: left;">
                {{ __('custom.date_registration') }}: {{ displayDate($application->registration_date) }} г.
            </td>
        </tr>
    </table>
</div>
</body>
</html>
