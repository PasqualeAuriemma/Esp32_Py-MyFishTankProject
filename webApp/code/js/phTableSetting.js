$(document).ready(function() {
  $('#phTableSetting').DataTable({
    "fnCreatedRow": function( nRow, aData, iDataIndex ) {
      $(nRow).attr('id', aData[0]);},
    "iDisplayLength": 5,
    "lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
    'serverSide':'true',
    "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
      $('td', nRow).css('background-color', 'rgba(33,37,41)');
    },
    "bProcessing": true,
    'paging':'true',
    'order':[],
    'ajax': {
      'url':'php/PHSettingTable/fetch_ph_settings.php',
      'type':'post'},
    "columnDefs": [{
      'targets': '_all',
      'orderable':false,
    }],
    "dom": 'lrtip',
  });
});

$(document).on('click','.editbtnPhS',function(event){
  $(this).removeClass().addClass("btn btn-info btn-sm editbtnConfirmPhS");
  var $row = $(this).closest("tr").off("mousedown");
  $row.find("td").not(':first').not(':last').not(':nth-child(4)').each(function(i, el) {
    var txt = $(this).text();
    //console.log(txt);
    $(this).empty().append($('<input>', {
      type : 'text',
      value : txt
    }).data('original-text', txt));
  });
});

$(document).on('click','.deleteBtnPhS',function(event){
  var table = $('#phTableSetting').DataTable();
  event.preventDefault();
  var id = $(this).data('id');
  //console.log(id);
  if(confirm("Are you sure want to delete this value?")){
    $.ajax({
      url:"php/PHSettingTable/delete_ph_settings.php",
      data:{id:id},
      type:"post",
      success:function(data){
        var json = JSON.parse(data);
        status = json.status;
        if(status=='success'){
          //table.fnDeleteRow( table.$('#' + id)[0] );
          $("#phTableSetting tbody").find(id).remove();
          //table.row($(this).closest("tr")) .remove();
          //$("#"+id).closest('tr').remove();
          table.draw();
          
        }else{
          alert('Failed Remove');
          return;
        }
      }
    });
  }else{
    return null;
  }
})
        
$(document).on('click','.editbtnConfirmPhS',function(event){
	$(this).removeClass().addClass("btn btn-info btn-sm editbtnPhS");
    var rowList = [];
    var id = $(this).data('id');
    //console.log(id);
    var $row = $(this).closest("tr");	
    $row.find('td').not(':first').not(':last').not(':nth-child(4)').each(function(i, el) {
      var $input = $(this).find('input');
      //$(this).text(true ? $input.val() : $input.data('original-text')); 
      $(this).text($input.val());
      rowList.push($input.val());
    });
     $.ajax({
      url:"php/PHSettingTable/update_ph_settings.php",
      type:"post",
      data:{ph:rowList[0], data_send:rowList[1], id:id},
      success:function(data){   
        var json = JSON.parse(data);
        var status = json.status;
          //alert('Modified');
          console.log(rowList);
          
      }
    }); 
       
});


