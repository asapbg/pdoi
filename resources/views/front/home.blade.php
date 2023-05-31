
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"><head id="j_idt2"><link type="text/css" rel="stylesheet" href="{{ asset('tmp/bootstrap.css') }}" /><link type="text/css" rel="stylesheet" href="{{ asset('tmp/fa-6.2.css') }}" /><link type="text/css" rel="stylesheet" href="{{ asset('tmp/materialize.css') }}" /><link type="text/css" rel="stylesheet" href="{{ asset('tmp/main.css') }}" /><link type="text/css" rel="stylesheet" href="{{ asset('tmp/style.css') }}" /><script type="text/javascript" src="{{ asset('tmp/jquery.js') }}"></script><script type="text/javascript" src="{{ asset('tmp/jquery-plugins.js') }}"></script><script type="text/javascript" src="{{ asset('tmp/core.js') }}"></script><link type="text/css" rel="stylesheet" href="{{ asset('tmp/components.css') }}" /><script type="text/javascript" src="{{ asset('tmp/components.js') }}"></script><script type="text/javascript">if(window.PrimeFaces){PrimeFaces.settings.locale='bg';}</script>
    <link type="text/css" rel="stylesheet" href="{{ asset('tmp/fontawesome-free/css/all.css') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- meta -->

    <title>Платформа за достъп до обществена информация</title>
    <!--Import materialize.css-->


    <!-- THEME -->
    <link rel="stylesheet" href="{{ asset('tmp/fa-5.3.css') }}" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous" /></head><body>

<!-- AJAX STATUS AND JSF MESSAGE INCLUDE --><div id="j_idt13"></div><script id="j_idt13_s" type="text/javascript">$(function(){PrimeFaces.cw("AjaxStatus","widget_j_idt13",{id:"j_idt13",start:function(){ajaxStatus(true)},complete:function(){ajaxStatus(false)}});});</script><span id="message"></span><script id="message_s" type="text/javascript">$(function(){PrimeFaces.cw("Growl","widget_message",{id:"message",sticky:false,life:5000,escape:true,keepAlive:false,msgs:[]});});</script>

<!-- SESSION EXPIRED MONITOR -->
<div class="ajax-status-wrapper">
    <div class="ajax">
        <div class="preloader-wrapper big active">
            <div class="spinner-layer spinner-blue">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                    <div class="circle"></div>
                </div><div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>

            <div class="spinner-layer spinner-red">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                    <div class="circle"></div>
                </div><div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>

            <div class="spinner-layer spinner-yellow">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                    <div class="circle"></div>
                </div><div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>

            <div class="spinner-layer spinner-green">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                    <div class="circle"></div>
                </div><div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- HEADER -->
<div class="header-wrapper">
    <div class="header">
        <form id="j_idt17" name="j_idt17" method="post" action="/PDoiExt/indexExt.jsf" enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="j_idt17" value="j_idt17" />

{{--            <div class="login">--}}

                <!-- Login icon -->
{{--                <div class="menu-option">--}}
{{--                    <a href="{{ route('lo') }}" title="Вход">--}}
{{--                        <span class="icon fas fa-sign-in-alt"></span></a>--}}
{{--                </div>--}}

                <!--  Trigger for change language dropdown -->
{{--                <div class="menu-option">--}}
{{--                    <a id="change-language" class="dropdown-button" href="#!" data-activates="selectLangDropdown" style="text-transform: uppercase;">--}}
{{--                        bg--}}
{{--                    </a>--}}
{{--                </div>--}}

                <!-- User options dropdown -->

                <!-- Instructions icon -->
{{--                <div class="menu-option">--}}
{{--                    <a title="Инструкции" href="#">--}}
{{--                        <i class="fas fa-question-circle"></i>--}}
{{--                    </a>--}}
{{--                </div>--}}

                <!-- Visual settings dropdown -->
{{--                <div class="menu-option menu-dropdown">--}}
{{--                    <a href="#" title="Опции за незрящи">--}}
{{--                        <span class="icon fas fa-eye-slash"></span>--}}
{{--                    </a>--}}
{{--                    <ul class="menu-dropdown-content">--}}
{{--                        <li style="display: flex; justify-content: space-around;">--}}
{{--                            <a href="#" title="Намали шрифта" onclick="font(false)">--}}
{{--                                <span id="decrease-font" class="icon fas fa-font"></span>--}}
{{--                                <span class="icon fas fa-caret-down font-caret"></span>--}}
{{--                            </a>--}}

{{--                            <a href="#" title="100%" onclick="resetFont()">100%</a>--}}

{{--                            <a href="#" title="Увеличи шрифта" onclick="font(true)">--}}
{{--                                <span id="increase-font" class="icon fas fa-font"></span>--}}
{{--                                <span class="icon fas fa-caret-up font-caret"></span>--}}
{{--                            </a>--}}

{{--                        </li>--}}
{{--                        <li>--}}
{{--                            <a href="#" title="Смени цвета" onclick="colorBlind()">--}}
{{--                                <span class="icon fas fa-palette"></span>--}}
{{--                            </a>--}}
{{--                        </li>--}}

{{--                    </ul>--}}
{{--                </div>--}}
{{--            </div>--}}

            <!--  Dropdown for changing language -->
{{--            <ul id="selectLangDropdown" class="dropdown-content">--}}
{{--                <li><a onclick="mojarra.jsfcljs(document.getElementById('j_idt17'),{'j_idt17:j_idt28':'j_idt17:j_idt28'},'');return false" href="#!">BG</a></li>--}}
{{--                <li><a onclick="mojarra.jsfcljs(document.getElementById('j_idt17'),{'j_idt17:j_idt31':'j_idt17:j_idt31'},'');return false" href="#!">EN</a></li>--}}
{{--            </ul>--}}

            <div class="container logo-container">
                <div class="logo">
                    <img src="{{ asset('img/gerb.png') }}" alt="Герб на България" />
                    <div class="logo-text">
                        <h1>МИНИСТЕРСКИ СЪВЕТ</h1>
                        <h2>Платформа за достъп до обществена информация</h2>
                    </div>
                </div>
            </div>

            <!-- MENU -->

            <!-- MENU -->
            <div class="menu">

			<span class="mobile-menu-handle" title="Меню">
				<span class="icon fas fa-bars"></span>
			</span>

                <ul class="nav">
                    <li>
                        <a href="#" title="">Начало</a>
                    </li>
                    <li>
                        <a href="#"> Подаване на заявление </a>
                    </li>
{{--                    <li>--}}
{{--                    </li>--}}
{{--                    <li>--}}
{{--                        <a href="#">Търсене</a>--}}
{{--                    </li>--}}
{{--                    <li>--}}
{{--                        <a href="#">Документи</a>--}}
{{--                    </li>--}}
{{--                    <li>--}}
{{--                        <a href="#">Статистика</a>--}}
{{--                    </li>--}}
                    <li>
                        <a href="#">Контакти</a>
                    </li>
                </ul>
            </div><input type="hidden" name="javax.faces.ViewState" id="j_id1:javax.faces.ViewState:0" value="-8042336814174574007:-8347393880128326818" autocomplete="off" />
        </form>
    </div>
</div>


<!-- CONTENT -->
<div class="wrapper">

    <!-- INSERT CONTENT HERE -->
    <style type="text/css">
        .wrapper {
            background:
                linear-gradient(to bottom, rgba(255,255,255,1) 0%, rgba(255,255,255,0.5) 25%, rgba(255,255,255,0.5) 50%, rgba(255,255,255,0.5) 75%, rgba(255,255,255,1) 100%),
                linear-gradient(to right,  rgba(255,255,255,1) 0%, rgba(255,255,255,0.5) 25%, rgba(255,255,255,0.5) 50%, rgba(255,255,255,0.5) 75%, rgba(255,255,255,1) 100%),
                url('./images/background.jpg');
            background-size: cover;
            background-position: center;
        }
    </style>
    <form id="j_idt40" name="j_idt40" method="post" action="/PDoiExt/indexExt.jsf" enctype="application/x-www-form-urlencoded">
        <input type="hidden" name="j_idt40" value="j_idt40" />


        <div class="container container-services">
            <div class="ui-g">

                <div class="ui-g-12 ui-md-12 ui-lg-4">
                    <div class="column-content">
                        <div class="title-icon">
                            <div class="icon fas fa-file-alt"></div>
                            <div class="title-nav">Заявление</div>
                        </div>
                        <div class="content">
                            <p>Подаване на заявление за достъп до обществена информация.</p>
                        </div>
                        <div class="button">
                            <a href="#" class="btn-primary">Избор</a>
                        </div>
                    </div>
                </div>

                <div class="ui-g-12 ui-md-12 ui-lg-4">
                    <div class="column-content box-green">

                        <div class="title-icon">
                            <div class="icon fas fa-search"></div>
                            <div class="title-nav">Търсене</div>
                        </div>
                        <div class="content">
                            <p>Обществена информация, публикувана на платформата.</p>
                        </div>
                        <div class="button">
                            <a href="#" class="btn-primary">Избор</a>
                        </div>
                    </div>
                </div>

                <div class="ui-g-12 ui-md-12 ui-lg-4">
                    <div class="column-content box-blue">
                        <div class="title-icon">
                            <div class="icon fas fa-video"></div>
                            <div class="title-nav">Видео инструкции</div>
                        </div>
                        <div class="content">
                            <p>Инструкции за работа с платформата за достъп на обществена информация.</p>
                        </div>
                        <div class="button">
                            <a href="#" class="btn-primary">Избор</a>
                        </div>
                    </div>
                </div>


            </div>

            <div class="ui-g">

                <div class="ui-g-12 ui-md-12 ui-lg-4">
                    <div class="column-content box-orange">
                        <div class="title-icon">
                            <div class="icon fas fa-layer-group"></div>
                            <div class="title-nav">Документи</div>
                        </div>
                        <div class="content">
                            <p>Нормативни документи: закони, инструции, наредби.</p>
                        </div>
                        <div class="button">
                            <a href="#" class="btn-primary">Избор</a>
                        </div>
                    </div>
                </div>

                <div class="ui-g-12 ui-md-12 ui-lg-4">
                    <div class="column-content box-1">

                        <div class="title-icon">
                            <div class="icon fas fa-pie-chart"></div>
                            <div class="title-nav">Статистика</div>
                        </div>
                        <div class="content">
                            <p><br><br></p>
                        </div>
                        <div class="button">
                            <a href="#" class="btn-primary">Избор</a>
                        </div>
                    </div>
                </div>

                <div class="ui-g-12 ui-md-12 ui-lg-4">
                    <div class="column-content box-2">
                        <div class="title-icon">
                            <div class="icon fas fa-users"></div>
                            <div class="title-nav">Контакти</div>
                        </div>
                        <div class="content">
                            <p><br><br></p>
                        </div>
                        <div class="button">
                            <a href="#" class="btn-primary">Избор</a>
                        </div>
                    </div>
                </div>

                <div class="ui-g">
                    <div class="ui-g-12 ui-md-12 ui-lg-12 ">
                        <div class="column-content box-white"><p class="ql-align-center">Платформата за достъп до обществена информация е създадена и се поддържа от администрацията на Министерския съвет съгласно чл. 15в, ал. 1 от Закона за достъп до обществена информация (ЗДОИ). Представлява единна, централна, публична уеб базирана информационна система, която осигурява електронно целия процес по подаване и разглеждане на заявление за достъп до информация, препращане по компетентност при необходимост, предоставяне на решение и публикуване на съответната информация от задължените по Закона за достъп до обществена информация субекти при спазване на защитата на личните данни на заявителя съгласно Закона за защита на личните данни.</p><p class="ql-align-center"><br></p>
                        </div>
                    </div>
                </div>
            </div>
        </div><input type="hidden" name="javax.faces.ViewState" id="j_id1:javax.faces.ViewState:1" value="-8042336814174574007:-8347393880128326818" autocomplete="off" />
    </form>

</div>

<!-- FOOTER -->
<div class="footer">
    <div class="footer-content">
        <div class="container-fluid">
            <div class="ui-g">
                <div class="ui-md-12">
                    <div class="info">
                        <p>Платформата за достъп до обществена информация е разработена в рамките на обществена поръчка с предмет: „Изработване, тестване и внедряване на Платформа за достъп до обществена информация и провеждане на свързано обучение“ в изпълнение на проект: „Подобряване на процесите, свързани с предоставянето, достъпа и повторното използване на информацията от обществения сектор“, финансиран по Оперативна програма „Добро управление“ по процедура BG05SFOP001-2.001 за директно предоставяне на безвъзмездна финансова помощ „Стратегически проекти в изпълнение на Стратегията за развитие на държавната администрация 2014 – 2020 г., ПОС, ПИК и НАТУРА 2000“.</p>
                    </div>

                </div>
            </div>
        </div>

        <div class="logos-container">
            <div style="margin-right: 20px; margin-bottom: 20px;">
                <img src="{{ asset('img/eu_white.png') }}" alt="EU" class="left" />
            </div>
            <div class="img-right-wrap">
                <img src="{{ asset('img/op_white.png') }}" alt="OP" class="right" />
            </div>
        </div>

    </div>
</div>
</body>
</html>
