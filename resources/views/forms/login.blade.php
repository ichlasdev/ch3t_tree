@extends('layouts.master')

@section('content')

<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100 p-t-50 p-b-90">
            <form class="login100-form validate-form flex-sb flex-w" action="/api/login" method="POST">
                <span class="login100-form-title p-b-51">
                    Login
                </span>

                <div class="wrap-input100 validate-input m-b-16" data-validate = "Email harus diisi">
                    <input class="input100" type="email" name="email" placeholder="Email" id="email">
                    <span class="focus-input100"></span>
                </div>


                <div class="wrap-input100 validate-input m-b-16" data-validate = "Password harus diisi">
                    <input class="input100" type="password" name="password" placeholder="Password" id="password">
                    <span class="focus-input100"></span>
                </div>

                <div class="flex-sb-m w-full p-t-3 p-b-24">
                    <div>
                        <a href="/register" class="txt1">
                            Belum punya akun? Register disini!
                        </a>
                    </div>
                </div>

                <div class="container-login100-form-btn m-t-17">
                    <button class="login100-form-btn">
                        Login
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
<div id="dropDownSelect1"></div>

@stop
