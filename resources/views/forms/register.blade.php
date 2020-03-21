@extends('layouts.master')

@section('content')

<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100 p-t-50 p-b-90">
            <form class="login100-form validate-form flex-sb flex-w" action="/api/register" method="POST">
                <span class="login100-form-title p-b-51">
                    Register
                </span>

                <div class="wrap-input100 validate-input m-b-16" data-validate = "Username harus diisi">
                    <input class="input100" type="text" name="name" placeholder="Nama Lengkap" id="name">
                    <span class="focus-input100"></span>
                </div>

                <div class="wrap-input100 validate-input m-b-16" data-validate = "Nomor telepon harus diisi">
                    <input class="input100" type="number" name="phone" placeholder="Nomor Telepon" id="phone">
                    <span class="focus-input100"></span>
                </div>

                <div class="wrap-input100 validate-input m-b-16" data-validate = "Email harus diisi">
                    <input class="input100" type="email" name="email" placeholder="Email" id="email">
                    <span class="focus-input100"></span>
                </div>

                <div class="wrap-input100 validate-input m-b-16" data-validate = "Password harus diisi">
                    <input class="input100" type="password" name="password" placeholder="Password">
                    <span class="focus-input100"></span>
                </div>

                <div class="wrap-input100 validate-input m-b-16" data-validate = "Ini juga harus diisi">
                    <input class="input100" type="password" name="password_confirmation" placeholder="Konfirmasi Password" id="password_confirmation" oninput="check(this)">
                    <span class="focus-input100"></span>
                </div>

                <div class="form-group">
                    <label for="exampleFormControlTextarea1"></label>
                    <input type="file" name="avatar" class="form-control">
                </div>

                <div class="flex-sb-m w-full p-t-3 p-b-24">
                    <div>
                        <a href="/login" class="txt1">
                            Sudah punya akun? Login disini!
                        </a>
                    </div>
                </div>

                <div class="container-login100-form-btn m-t-17">
                    <button class="login100-form-btn">
                        Register
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
<div id="dropDownSelect1"></div>

@stop
