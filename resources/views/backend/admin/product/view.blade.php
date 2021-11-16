<div class="row">
    <div class="col-md-12 col-sm-12 table-responsive">
        <table id="view_details" class="table table-bordered table-hover">
            <tbody>
            <tr>
                <td class="subject"> Title</td>
                <td> {{ $product->title }} </td>
            </tr>
            <tr>
                <td class="subject"> Description</td>
                <td> {{ $product->description }} </td>
            </tr>
            <tr>
                <td class="subject">  Catagory </td>
                <td>
                    @if(isset($product->category))
                    {{ucfirst($product->category->title)}}
                    @else
                    No Catagory Found
                    @endif
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-4">
        <img src="{{asset($product->file_path)}}" class="img-responsive img-circle" width="150px"/><br/><br/>
    </div>
</div>