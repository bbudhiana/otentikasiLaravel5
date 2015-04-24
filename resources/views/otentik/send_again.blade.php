@extends('utama')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Send Activation Code</div>
                    <div class="panel-body">

                        @include('otentik.partials.message')

                        <form class="form-horizontal" role="form" method="POST" action="/otentik/send_again">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group">
                                <label class="col-md-4 control-label">Email
                                </label>

                                <div class="col-md-6">
                                    <input type="email" placeholder="Put your email here" class="form-control"
                                           name="email" value="{{ old('email') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary" style="margin-right: 15px;">
                                        Send Me
                                    </button>
                                </div>
                            </div>
                            <div class="alert alert-warning">
                                <strong>Perhatian!</strong> Akun anda masih belum dapat digunakan.<br>

                                <ul>
                                    <li>Anda sudah melakukan registrasi namun belum melakukan aktifasi</li>
                                    <li>Silahkan buka email anda dan lakukan aktifasi</li>
                                    <li>Jika masih bermasalah silahkan tunggu beberapa saat lagi untuk melakukan
                                        login/registrasi ulang
                                    </li>
                                </ul>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
