@extends('utama')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">My Profile</div>
				<div class="panel-body">

					@include('otentik.partials.message')

					<div class="tabbable">
						<ul class="nav nav-tabs tab-padding tab-space-3 tab-blue" id="myTab4">
							<li @if (!Session::has('panel')) class="active" @endif>
								<a data-toggle="tab" href="#panel_overview">
									Overview
								</a>
							</li>
							<li @if (Session::has('panel') && (Session::get('panel')=='profile')) class="active" @endif>
								<a data-toggle="tab" href="#panel_edit_profile">
									Edit Profile
								</a>
							</li>
							<li @if (Session::has('panel') && (Session::get('panel')=='account')) class="active" @endif>
								<a data-toggle="tab" href="#panel_edit_account">
									Edit Account
								</a>
							</li>
						</ul>
						<div class="tab-content">
							<div id="panel_overview" class="tab-pane @if (!Session::has('panel')) in active @endif">
								<form class="form-horizontal">
									<div class="form-group">
										<label class="col-sm-2 control-label">Email</label>
										<div class="col-sm-10">
											<p class="form-control-static">{!! Auth::user()->email !!}</p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">Username</label>
										<div class="col-sm-10">
											<p class="form-control-static">{!! Auth::user()->username !!}</p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">Fullname</label>
										<div class="col-sm-10">
											<p class="form-control-static">{!! Auth::user()->userprofile->fullname !!}</p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">Group</label>
										<div class="col-sm-10">
											<p class="form-control-static">{!! Auth::user()->userprofile->group->name !!}</p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">Phone</label>
										<div class="col-sm-10">
											<p class="form-control-static">{!! Auth::user()->userprofile->phone !!}</p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">Birthday</label>
										<div class="col-sm-10">
											<p class="form-control-static">{!! Auth::user()->userprofile->day_of_birth !!}</p>
										</div>
									</div>
								</form>
							</div>
							<div id="panel_edit_profile" class="tab-pane @if (Session::has('panel') && (Session::get('panel')=='profile')) in active @endif">
								<form class="form-horizontal" role="form" method="POST" action="/otentik/profile">
									<input type="hidden" name="_token" value="{{ csrf_token() }}">

									<div class="form-group">
										<label class="col-sm-2 control-label">Fullname</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" placeholder="Fullname" name="fullname" value="{{ Auth::user()->userprofile->fullname }}">
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2 control-label">Phone</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" placeholder="Phone" name="phone" value="{{ Auth::user()->userprofile->phone }}">
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2 control-label">Day of Birth</label>
										<div class="col-sm-10">
											<input type="date" class="form-control" placeholder="Day Of Birth" name="day_of_birth" value="{{ Auth::user()->userprofile->day_of_birth }}">
										</div>
									</div>

									<div class="form-group">
										<div class="col-sm-12 col-md-offset-4">
											<button type="submit" class="btn btn-primary">
												Update Profile
											</button>
										</div>
									</div>
								</form>
							</div>
							<div id="panel_edit_account" class="tab-pane @if (Session::has('panel') && (Session::get('panel')=='account')) in active @endif">
								<form class="form-horizontal" role="form" method="POST" action="/otentik/account">
									<input type="hidden" name="_token" value="{{ csrf_token() }}">

									<div class="form-group">
										<label class="col-sm-2 control-label">email</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" placeholder="Email" name="email" value="{{ Auth::user()->email }}">
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2 control-label">Username</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" placeholder="Username" name="username" value="{{ Auth::user()->username }}">
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2 control-label">Password</label>
										<div class="col-sm-10">
											<input type="password" placeholder="Password" class="form-control" name="password">
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2 control-label">Confirm Password</label>
										<div class="col-sm-10">
											<input type="password" placeholder="Password Confirmation" class="form-control" name="password_confirmation">
										</div>
									</div>

									<div class="form-group">
										<div class="col-sm-12 col-md-offset-4">
											<button type="submit" class="btn btn-primary">
												Update Account
											</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection
