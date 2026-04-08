<div class="modal-body">
    <div class="row mb-3">
        <div class="col-md-12 mb-2">
            <div class="info">
                <strong>{{__('Branch')}} : </strong>
                <span>{{ !empty($indicator->branches)?$indicator->branches->name:''}}</span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info">
                <strong>{{__('Department')}} : </strong>
                <span>{{ !empty($indicator->departments)?$indicator->departments->name:'' }}</span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info">
                <strong>{{__('Designation')}} : </strong>
                <span>{{ !empty($indicator->designations)?$indicator->designations->name:'' }}</span>
            </div>
        </div>

    </div>

    @foreach($performance as $performances)
    <div class="row">
        <div class="col-md-12">
            <h5 class="mb-2">{{$performances->name}}</h5>
            <hr class="mt-0">
        </div>
        @foreach($performances->types as $types)
            <div class="col-6">
                {{$types->name}}
            </div>
            <div class="col-6">
                <fieldset id='demo1' class="rating">
                    <input class="stars" type="radio" id="technical-5-{{$types->id}}" name="rating[{{$types->id}}]" value="5" {{ (isset($ratings[$types->id]) && $ratings[$types->id] == 5)? 'checked':''}} disabled>
                    <label class="full" for="technical-5-{{$types->id}}" title="Awesome - 5 stars"></label>
                    <input class="stars" type="radio" id="technical-4-{{$types->id}}" name="rating[{{$types->id}}]" value="4" {{ (isset($ratings[$types->id]) && $ratings[$types->id] == 4)? 'checked':''}} disabled>
                    <label class="full" for="technical-4-{{$types->id}}" title="Pretty good - 4 stars"></label>
                    <input class="stars" type="radio" id="technical-3-{{$types->id}}" name="rating[{{$types->id}}]" value="3" {{ (isset($ratings[$types->id]) && $ratings[$types->id] == 3)? 'checked':''}} disabled>
                    <label class="full" for="technical-3-{{$types->id}}" title="Meh - 3 stars"></label>
                    <input class="stars" type="radio" id="technical-2-{{$types->id}}" name="rating[{{$types->id}}]" value="2" {{ (isset($ratings[$types->id]) && $ratings[$types->id] == 2)? 'checked':''}} disabled>
                    <label class="full" for="technical-2-{{$types->id}}" title="Kinda bad - 2 stars"></label>
                    <input class="stars" type="radio" id="technical-1-{{$types->id}}" name="rating[{{$types->id}}]" value="1" {{ (isset($ratings[$types->id]) && $ratings[$types->id] == 1)? 'checked':''}} disabled>
                    <label class="full" for="technical-1-{{$types->id}}" title="Sucks big time - 1 star"></label>
                </fieldset>
            </div>
        @endforeach
    </div>
    @endforeach
</div>



