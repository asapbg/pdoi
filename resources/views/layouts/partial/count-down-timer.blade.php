<input type="hidden" id="sess_check" value="{{ auth()->user() ? '1' : '0'}}">

@if(auth()->user())
  <div class="session-timer text-md-left text-center" style="transition: all 0.4s ease-in; padding: 4px 8px 5px 8px; border-radius: 4px; cursor: default;">
    <span style="" class="d-inline-block mr-2"><i class="far fa-clock"></i></span>
    <div id="timer" data-toggle="tooltip" title="Оставащо време от тази сесия" class="d-inline-block">
      <span id="timer-hours" class="d-inline-block text-center" style="width: 16px;"></span>
      <span style="width: 8px;" class="d-inline-block text-center">:</span>
      <span id="timer-minutes" style="width: 16px;" class="d-inline-block text-center"></span>
      <span style="width: 8px;" class="d-inline-block text-center">:</span>
      <span id="timer-seconds" style="width: 16px;" class="d-inline-block text-center"></span>
    </div>
      <span class="d-md-none fsi">(Оставащо време от тази сесия)</span>
    <div class="d-inline-block">
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
      </form>
    </div>
  </div>
@endif

@push('scripts')
  <script type="text/javascript"  nonce="2726c7f26c">
      $(document).ready(function (){
          var last_login = "{{ \Carbon\Carbon::now() }}";
          var session_lifetime = "{{ \Session::get('user_session_time_limit') }}";
          session_lifetime = session_lifetime * 60;

          var remindedSessionEndInAdmin = false;
          var remindedSessionEndInFront = false;

          var sess_check = $('#sess_check').val();
          if (sess_check == '1') {

              async function makeTimer() {
                  var endTime = (Date.parse(last_login) / 1000) + session_lifetime + 1;
                  var now = new Date();
                  now = (Date.parse(now) / 1000);

                  var timeLeft = endTime - now;

                  var days = Math.floor(timeLeft / 86400);
                  var hours = Math.floor((timeLeft - (days * 86400)) / 3600);
                  var minutes = Math.floor((timeLeft - (days * 86400) - (hours * 3600 )) / 60);
                  var seconds = Math.floor((timeLeft - (days * 86400) - (hours * 3600) - (minutes * 60)));

                  if (timeLeft < 0) {
                      hours = 0;
                      minutes = 0;
                      seconds = 0;
                      $('#logout-form').submit();
                      clearInterval();
                  }

                  if (hours < "10") { hours = "0" + hours; }
                  if (minutes < "10") { minutes = "0" + minutes; }
                  if (seconds < "10") { seconds = "0" + seconds; }

                  $("#timer").html(hours + " : " + minutes + " : " + seconds);

                  // Fire notification 5 minutes (300 s) before session ends
                  if ((timeLeft == 300) || (timeLeft == 298)) {
                      if(($("#front-timer").length) && !remindedSessionEndInFront) {
                          remindSessionEndsIn5Minutes();
                          remindedSessionEndInAdmin = true;
                      }

                      if(($("#admin-timer").length) && !remindedSessionEndInAdmin) {
                          remindSessionEndsIn5Minutes();
                          remindedSessionEndInAdmin = true;
                      }
                  }

                  // Log-out user when session ends
                  if (timeLeft == 0) {
                      $('#logout-form').submit();
                      clearInterval();
                  }
              }

              function remindSessionEndsIn5Minutes() {
                  toastr.options.closeButton = true;
                  toastr.options.timeOut = 0;
                  toastr.options.extendedTimeOut = 0;
                  toastr.warning('Внимание, остават по-малко от 5 минути от текущата Ви сесия. Моля запазете данните, ако имате незапазени промени.');
                  $('#toast-container').removeClass("toast-top-right").addClass("toast-bottom-right");
              }

              setInterval(function() { makeTimer(); }, 1000);
          }
      });

  </script>
@endpush
