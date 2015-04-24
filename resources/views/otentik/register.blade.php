@extends('utama')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Register</div>
				<div class="panel-body">

					@include('otentik.partials.message')

					<form class="form-horizontal" role="form" method="POST" action="/otentik/register">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<div class="form-group">
							<label class="col-md-4 control-label">Fullname</label>
							<div class="col-md-6">
								<input type="text" class="form-control" placeholder="Fullname" name="fullname" value="{{ old('fullname') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">User name</label>
							<div class="col-md-6">
								<input type="text" class="form-control" placeholder="Username" name="username" value="{{ old('username') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">E-Mail Address</label>
							<div class="col-md-6">
								<input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Password</label>
							<div class="col-md-6">
								<input type="password" placeholder="Password" class="form-control" name="password">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Confirm Password</label>
							<div class="col-md-6">
								<input type="password" placeholder="Password Confirmation" class="form-control" name="password_confirmation">
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
							<label class="col-md-4 control-label">&nbsp;</label>
							<div class="col-md-6">
								<input type="checkbox" class="grey agree" id="agree" name="agree">
								I agree to the Terms of Service and Privacy Policy
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">
									Register
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
