@extends('utama')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Reset Password</div>
				<div class="panel-body">

					@include('otentik.partials.message')

					<form class="form-horizontal" role="form" method="POST" action="/otentik/reset_password">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="token_key" value="{{ $token_key }}">
						<input type="hidden" name="user_id" value="{{ $user_id }}">
						{{--<div class="form-group">
							<label class="col-md-4 control-label">E-Mail Address</label>
							<div class="col-md-6">
								<input type="email"  placeholder="Your Email" class="form-control" name="email" value="{{ old('email') }}">
							</div>
						</div>--}}

						<div class="form-group">
							<label class="col-md-4 control-label">Password</label>
							<div class="col-md-6">
								<input type="password" placeholder="Password" class="form-control" name="password">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Confirm Password</label>
							<div class="col-md-6">
								<input type="password" placeholder="Confirm Password" class="form-control" name="password_confirmation">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">
									Reset Password
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
