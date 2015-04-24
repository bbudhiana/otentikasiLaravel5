@extends('utama')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Login</div>
				<div class="panel-body">

					@include('otentik.partials.message')

					<form class="form-horizontal" role="form" method="POST" action="/otentik/login">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<div class="form-group">
							<label class="col-md-4 control-label">
								@if (config('keamanan.login_by_username') AND config('keamanan.use_username'))
								Login
								@else
								Email
								@endif
							</label>
							<div class="col-md-6">
								<input type="login" class="form-control" name="login" value="{{ old('login') }}">
								{{-- $errors->first('login') --}}
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Password</label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password">
							</div>
						</div>
						@if ($is_need_captcha)
						<div class="form-group">
							<label class="col-md-4 control-label">&nbsp;</label>
							<div class="col-md-6">
								{!! app('captcha')->display(); !!}
							</div>
						</div>
						@endif
						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="remember"> Remember Me
									</label>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary" style="margin-right: 15px;">
									Login
								</button>

								<a href="/otentik/forgot_password">Forgot Your Password?</a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
