<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
</head>
<body onload="document.forms[0].submit()">
<noscript>
    <p>
        <strong>Note:</strong> Since your browser does not support JavaScript,
        you must press the Continue button once to proceed.
    </p>
</noscript>

<form action="{{ env('E_AUTH_ENDPOINT_URL') }}" method="post">
    <div>
        <input type="hidden" name="SAMLRequest" value="{{ isset($params) && sizeof($params) && isset($params['SAMLRequest']) ? $params['SAMLRequest']: '' }}"/>
    </div>
    <noscript>
        <div>
            <input type="submit" value="Continue"/>
        </div>
    </noscript>
</form>

</body>
