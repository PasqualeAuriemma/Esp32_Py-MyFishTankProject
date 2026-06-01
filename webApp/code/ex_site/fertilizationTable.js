$(document).ready(function() {
  $('#fertilizationTable').DataTable({
    "fnCreatedRow": function( nRow, aData, iDataIndex ) {
      $(nRow).attr('id', aData[0]);},
    "dom": 'lfrtip',
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
      'url':'Fertilization/fetch_quantities.php',
      'type':'post'},
    "columnDefs": [{
      'targets': '_all',
      'orderable':false,
    }],
    "dom": 'lrtip',
  });
});
$(document).on('submit','#addQuantities',function(e){
  e.preventDefault();
  var k= $('#addKField').val();
  var mg= $('#addMgField').val();
  var fe= $('#addFeField').val();
  var rinverdente = $('#addRinverdenteField').val();
  var p= $('#addPField').val();
  var npk= $('#addNPKField').val();
  var n= $('#addNField').val();
 
    $.ajax({
      url:"Fertilization/add_quantities.php",
      type:"post",
      data:{potassio:k, magnesio:mg, ferro:fe, rinverdente:rinverdente, fosforo:p, azoto:n, npk:npk},
      success:function(data){
        var json = JSON.parse(data);
        var status = json.status;
        if(status=='true'){
          mytable =$('#fertilizationTable').DataTable();
          mytable.draw();
          $('#addFertilizationModal').modal('hide');
          var frm = document.getElementsByName('addQuantities')[0];
          frm.reset();  // Reset all form data
        }else{
          alert('failed');
        }
      }
    });
  
});
$(document).on('submit','#updateQuantities',function(e){  
   console.log("Pasqule");

  e.preventDefault();
  //var tr = $(this).closest('tr');
  var key= $('#keyQField').val();
  var k= $('#kField').val();
  var mg= $('#mgField').val();
  var fe= $('#feField').val();
  var rinverdente= $('#rinverdenteField').val();
  var p= $('#pField').val();
  var n= $('#nField').val();
  var _npk= $('#npkField').val();
  var trid= $('#tridQ').val();
  var id= $('#idQ').val();
  var date= $('#dateQ').val();

    $.ajax({
      url:"Fertilization/update_quantities.php",
      type:"post",
      data:{potassio:k, magnesio:mg, ferro:fe, rinverdente:rinverdente, fosforo:p, azoto:n , npk:_npk, id:id},
      success:function(data){
        var json = JSON.parse(data);
        var status = json.status;
        if(status=='true'){
          table =$('#fertilizationTable').DataTable();
          // table.cell(parseInt(trid) - 1,0).data(id);
          // table.cell(parseInt(trid) - 1,1).data(username);
          // table.cell(parseInt(trid) - 1,2).data(email);
          // table.cell(parseInt(trid) - 1,3).data(mobile);
          // table.cell(parseInt(trid) - 1,4).data(city);
          var button =   '<td><a href="javascript:void();" data-id="' +id + '" class="btn btn-info btn-sm editbtnF" ><i class="mdi mdi-table-edit"></i></a>  <a href="#!"  data-id="' +id + '"   class="btn btn-danger btn-sm deleteBtnF" ><i class="mdi mdi-table-row-remove"></i></a></td>';
          var row = table.row("[id='"+trid+"']");
          var dateClass = new Date(date);
          var MyDateString = ('0' + dateClass.getDate()).slice(-2) + '/'
          + ('0' + (dateClass.getMonth()+1)).slice(-2) + '/'
          + dateClass.getFullYear();
          row.row("[id='" + trid + "']").data([MyDateString, k, mg, fe, rinverdente, p, n, _npk, button]);
          $('#updateFertilizationModal').modal('hide');
        }else{
          alert('failed');
        }
      }
    });
 
});


$(document).on('click','.editbtnF ',function(event){
  console.log("Pasqule");
  $(this).removeClass().addClass("btn btn-info btn-sm editbtnConfirmF");
  var $row = $(this).closest("tr").off("mousedown");
  $row.find("td").not(':first').not(':last').each(function(i, el) {
    var txt = $(this).text();
    $(this).empty().append($('<input>', {
      type : 'text',
      value : txt
    }).data('original-text', txt));
  });
  /*
  var table = $('#fertilizationTable').DataTable();
  var trid = $(this).closest('tr').attr('id');
  // console.log(selectedRow);
  var id = $(this).data('id');
  $('#updateFertilizationModal').modal('show');
  $.ajax({
    url:"Fertilization/get_single_quantity.php",
    data:{id:id},
    type:'post',
    success:function(data){
      var json = JSON.parse(data);
      $('#kField').val(json.k);
      $('#mgField').val(json.mg);
      $('#feField').val(json.fe);
      $('#rinverdenteField').val(json.rinverdente);
      $('#pField').val(json.p);
      $('#nField').val(json.n);
      $('#npkField').val(json.npk);
      $('#idQ').val(id);
      $('#dateQ').val(json.data);
      $('#tridQ').val(trid);
    }
  })
  */
});

$(document).on('click','.deleteBtnF',function(event){
  var table = $('#fertilizationTable').DataTable();
  event.preventDefault();
  var id = $(this).data('id');
  if(confirm("Are you sure want to delete this quantities? ")){
    $.ajax({
      url:"Fertilization/delete_quantities.php",
      data:{id:id},
      type:"post",
      success:function(data){
        var json = JSON.parse(data);
        status = json.status;
        if(status=='success'){
          //table.fnDeleteRow( table.$('#' + id)[0] );
          $("#fertilizationTable tbody").find(id).remove();
          //table.row($(this).closest("tr")) .remove();
          //$("#"+id).closest('tr').remove();
          table.draw();
          showVolumes();
        }else{
          alert('Failed');
          return;
        }
      }
    });
  }else{
    return null;
  }
})
        
$(document).on('click','.addFertilizationBtn',function(event){
  event.preventDefault();
  var key= $('#addKeyQField').val();
  var k= $('#addKField').val();
  var mg= $('#addMgField').val();
  var fe= $('#addFeField').val();
  var rinverdente = $('#addRinverdenteField').val();
  var p= $('#addPField').val();
  var npk= $('#addNPKField').val();
  var n= $('#addNField').val();
    $.ajax({
      url:"Fertilization/add_quantities.php",
      type:"post",
      data:{potassio:k, magnesio:mg, ferro:fe, rinverdente:rinverdente, fosforo:p, azoto:n, npk:npk},
      success:function(data){
        var json = JSON.parse(data);
        var status = json.status;
        if(status=='true'){
          mytable =$('#fertilizationTable').DataTable();
          mytable.draw();
          showVolumes();
        }else{
          alert('failed');
        }
      }
    });
  
});

$(document).on('click','.editbtnConfirmF',function(event){
	$(this).removeClass().addClass("btn btn-info btn-sm editbtnF");
    var rowList = [];
    var id = $(this).data('id');
    console.log(id);
    var $row = $(this).closest("tr");	
    $row.find('td').not(':first').not(':last').each(function(i, el) {
      var $input = $(this).find('input');
      //$(this).text(true ? $input.val() : $input.data('original-text')); 
      $(this).text($input.val());
      rowList.push($input.val());
    });
     $.ajax({
      url:"PHSettingTable/update_quantities.php",
      type:"post",
      data:{potassio:rowList[0], magnesio:rowList[1], ferro:rowList[2], rinverdente:rowList[3], fosforo:rowList[4], azoto:rowList[5] , npk:rowList[6], id:id},
      success:function(data){   
        var json = JSON.parse(data);
        var status = json.status;
          //alert('Modified');
          console.log(rowList);
          showVolumes();
      }
    }); 
       
});


