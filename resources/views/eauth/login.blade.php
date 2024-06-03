<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
</head>
<body>
<noscript>
    <p>
        <strong>Note:</strong> Since your browser does not support JavaScript,
        you must press the Continue button once to proceed.
    </p>
</noscript>

<form action="{{ config('eauth.endpoint') }}" method="post">
    <div>
        <input type="hidden" name="SAMLRequest" value="{{ isset($params) && sizeof($params) && isset($params['SAMLRequest']) ? $params['SAMLRequest']: '' }}"/>
    </div>
    <noscript>
        <div>
            <input type="submit" value="Continue"/>
        </div>
    </noscript>
</form>

<script type="text/javascript"  nonce="2726c7f26c" defer>
    document.addEventListener( "DOMContentLoaded", function (){
        document.forms[0].submit();
    });
</script>
</body>
