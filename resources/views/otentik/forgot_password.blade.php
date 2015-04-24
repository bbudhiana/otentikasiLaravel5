@extends('utama')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Forgot Password</div>
				<div class="panel-body">

					@include('otentik.partials.message')

					<form class="form-horizontal" role="form" method="POST" action="/otentik/forgot_password">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<div class="form-group">
							<label class="col-md-4 control-label">Email
							</label>
							<div class="col-md-6">
								<input type="login" placeholder="Put your email here" class="form-control" name="login" value="{{ old('login') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Verification</label>
							<div class="col-md-6">
								{!! app('captcha')->display(); !!}
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary" style="margin-right: 15px;">
									Send Me
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
