@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>
                <div class="panel-body">
                   <h1>Hello, {{ Auth::user()->name }}</h1>
	                <br/>

	                @if (isset($error))
	                <p>
	                <div class="alert alert-danger">
		                <strong>Error</strong> {{ $error['description'] }}
	                </div>
	                </p>
	                @endif
	               <p>
		                <form>
			               <label for="buyer-code">Purchase Code</label>
			               <input type="text" id="buyer-code" name="purchasecode">
			               <input type="submit">
		               </form>
	                </p>
	                @if (isset($data))
	                <p>
	                    <dl>
		                    <dt>Item Name</dt><dd>{{ $data['item']['name'] }}</dd>
		                    <dt>Sale Date</dt><dd>{{ date('F d, Y', strtotime($data['sold_at'])) }}</dd>
							<dt>Supported Until</dt><dd>{{ date('F d, Y', strtotime($data['supported_until'])) }}</dd>
		                    <dt>Buyer username</dt><dd>{{ $data['buyer'] }}</dd>
	                    </dl>
	                </p>
	                @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
