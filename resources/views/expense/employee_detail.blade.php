@if(!empty($employee))
    <div class="row">
        <div class="col-md-5">
            <h6>{{__('Employee Details')}}</h6>
            <div class="bill-to">
                @if(!empty($employee->name))
                    <small>
                        <span>{{$employee->name}}</span><br>
                        <span>{{$employee->email}}</span><br>
                        <span>{{$employee->phone}}</span><br>
                        <span>{{$employee->address}}</span><br>

                    </small>
                @else
                    <br> -
                @endif
            </div>
        </div>

        <div class="col-md-2">
            <a href="#" id="remove" class="text-sm">{{__(' Remove')}}</a>
        </div>
    </div>
@endif
