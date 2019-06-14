@extends('layouts.app')
@section('content')
<div class="container">
  <p>&nbsp;</p>
    <a class="btn btn-success" href="javascript:void(0)" id="createNewItem"> Create New Item</a>
    <p class="clearfix">&nbsp;</p>
    <table class="table table-bordered data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Description</th>
                <th width="280px">Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
   
<div class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>
            </div>
            <div class="modal-body">
    <div class="alert alert-danger" style="display: none;">
        
    </div>
                <form id="itemForm" name="itemForm" class="form-horizontal">
                   <input type="hidden" name="item_id" id="item_id">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required="">
                        </div>
                    </div>
     
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Details</label>
                        <div class="col-sm-12">
                            <textarea id="description" name="description" required="" placeholder="Enter Details" class="form-control"></textarea>
                        </div>
                    </div>
      
                    <div class="col-sm-offset-2 col-sm-10">
                     <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes
                     </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
@section('javascript')
<script type="text/javascript">
     $(function () {
     $('#ajaxModel').on('shown.bs.modal', function() {
      jQuery('.alert-danger').html("")
      jQuery('.alert-danger').hide();
          // jQuery('.alert-danger').find('li').remove();
    })
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('items.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'description', name: 'description'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
     
    $('#createNewItem').click(function () {
        $('#saveBtn').val("create-Item");
        $('#item_id').val('');
        $('#itemForm').trigger("reset");
        $('#modelHeading').html("Create New Item");
        $('#ajaxModel').modal('show');
    });
    
    $('body').on('click', '.editItem', function () {
      var item_id = $(this).data('id');
      $.get("{{ route('items.index') }}" +'/' + item_id +'/edit', function (data) {
          $('#modelHeading').html("Edit Item");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal('show');
          $('#item_id').val(data.id);
          $('#name').val(data.name);
          $('#description').val(data.description);
      })
   });
    
    $('#saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html('Sending..');
        $(this).prop('disabled',true);
        $.ajax({
          data: $('#itemForm').serialize(),
          url: "{{ route('items.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
           $('#saveBtn').prop('disabled',false);
           jQuery('.alert-danger').html("");
             if(data.status==1){
                //alert('here');
                 $('#itemForm').trigger("reset");
                 $('#ajaxModel').modal('hide');
                  table.draw();
             } else{
                jQuery.each(data.errors, function(key, value){
                            jQuery('.alert-danger').show();
                            jQuery('.alert-danger').append('<p>'+value+'</p>');
              });
             }
            $('#saveBtn').html('Save Changes');
         
          }
      });
    });
    
    $('body').on('click', '.deleteItem', function () {
     
        var item_id = $(this).data("id");
        confirm("Are You sure want to delete !");
      
        $.ajax({
            type: "DELETE",
            url: "{{ route('items.store') }}"+'/'+item_id,
            success: function (data) {
                table.draw();
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });
     
  });

</script>

@endsection
   