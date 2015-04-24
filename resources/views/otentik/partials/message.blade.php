@if(Session::has('message'))
    <div class="alert alert-success">
        <i class="fa fa-check-circle"></i> {{ Session::get('message') }}
    </div>
@endif
@if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were some problems with your input.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif