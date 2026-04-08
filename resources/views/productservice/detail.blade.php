<div class="modal-body p-0">
    <div class="card mb-0 border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">{{__('Warehouse') }}</th>
                        <th scope="col" class="text-center">{{__('Quantity')}}</th>
                    </tr>
                    </thead>
                    <tbody>

                    @forelse ($products as $product)
                        @if(!empty($product->warehouse))
                            <tr>
                                <td class="align-middle">{{ !empty($product->warehouse)?$product->warehouse->name:'-' }}</td>
                                <td class="align-middle text-center">{{ $product->quantity }}</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">{{__(' Product not select in warehouse')}}</td>
                        </tr>
                    @endforelse

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
