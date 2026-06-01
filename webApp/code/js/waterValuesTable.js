$(document).ready(function() {
  $('#waterValuesTable').DataTable({
    "fnCreatedRow": function( nRow, aData, iDataIndex ) {
      $(nRow).attr('id', aData[0]);},
    "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
      $('td', nRow).css('background-color', 'rgba(33,37,41)');
    },    
    "pageLength": 5,
    "lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ],
    'serverSide':'true',
    'bProcessing':'true',
    "dom": 'Blrtip',
    'paging':'true',
    'order':[],
    "columns": [
    null ,
    {"width": "30%" },
    {"width": "30%" },
    {"width": "30%" },
    null,null,null,null,
    null,
    null,
  ],
  fixedColumns: true,
    "buttons": [{extend: 'collection', text: 'Export', buttons: ['copy',
                    {extend: 'excelHtml5', text: 'Excel'},
                    {extend: 'csvHtml5', text: 'csv', fieldSeparator: ';', fieldBoundary: ''},
                    {extend: "pdfHtml5", customize: function(doc) { 
                                                    doc.styles.tableBodyEven.alignment = 'center';
                                                    doc.styles.tableBodyOdd.alignment = 'center';}},
                    'print']}], 
    'ajax': {'url':'php/waterValuesDirectory/fetch_data.php', 'type':'post'},
    "columnDefs": [{'targets': '_all', 'orderable':false}],
    
  });
});
$(document).on('submit','#addUser',function(e){
  e.preventDefault();
  var ecP= $('#addecPField').val();
  var ecA= $('#addecAField').val();
  var ph= $('#addphField').val();
  var no2= $('#addNo2Field').val();
  var no3= $('#addNo3Field').val();
  var gh= $('#addGHField').val();
  var kh= $('#addKHField').val();
  var po4= $('#addPo4Field').val();
  
    $.ajax({
      url:"php/waterValuesDirectory/add_user.php",
      type:"post",
      data:{ecP:ecP, ecA:ecA, ph:ph, no2:no2, no3:no3, gh:gh, kh:kh, po4:po4},
      success:function(data){
        var json = JSON.parse(data);
        var status = json.status;
        if(status=='true'){
          mytable =$('#waterValuesTable').DataTable();
          mytable.draw();
          $('#addWVModal').modal('hide');
          var frm = document.getElementsByName('addUser')[0];
          frm.reset();  // Reset all form data
        }else{
          alert('failed');
        }
      }
    });
  
});
$(document).on('submit','#updateUser',function(e){
  e.preventDefault();
  //var tr = $(this).closest('tr');
  //var key= $('#keyField').val();
  var ecP= $('#ecPField').val();
  var ecA= $('#ecAField').val();
  var ph= $('#phField').val();
  var no2= $('#no2Field').val();
  var no3= $('#no3Field').val();
  var gh= $('#ghField').val();
  var kh= $('#khField').val();
  var po4= $('#po4Field').val();
  var trid= $('#trid').val();
  var id= $('#id').val();
  var date= $('#date').val();
  //if (key == "Pia12"){
    //if(no2 != '' && no3 != '' && gh != '' && kh != '' ) {
    $.ajax({
      url:"php/waterValuesDirectory/update_user.php",
      type:"post",
      data:{ecP:ecP, ecA:ecA, ph:ph, no2:no2, no3:no3, gh:gh, kh:kh, po4:po4, id:id},
      success:function(data){
        var json = JSON.parse(data);
        var status = json.status;
        if(status=='true'){
          table =$('#waterValuesTable').DataTable();
          // table.cell(parseInt(trid) - 1,0).data(id);
          // table.cell(parseInt(trid) - 1,1).data(username);
          // table.cell(parseInt(trid) - 1,2).data(email);
          // table.cell(parseInt(trid) - 1,3).data(mobile);
          // table.cell(parseInt(trid) - 1,4).data(city);
          var button = '<td><a href="javascript:void();" data-id="' +id + '" class="btn btn-info btn-sm editbtn">Edit</a>  <a href="#!"  data-id="' +id + '"  class="btn btn-danger btn-sm deleteBtn">Del</a></td>';
          var row = table.row("[id='"+trid+"']");
          var dateClass = new Date(date);
          var MyDateString = ('0' + dateClass.getDate()).slice(-2) + '/'
          + ('0' + (dateClass.getMonth()+1)).slice(-2) + '/'
          + dateClass.getFullYear();
          row.row("[id='" + trid + "']").data([MyDateString, ecP, ecA, ph, no2, no3, gh, kh, po4, button]);
          $('#updateVWModal').modal('hide');
        }else{
          alert('failed');
        }
      }
    });
    //}else{
    //   alert('Fill all the required fields');
    //}
 // }else{
   // alert('Key wrong');
 // }
});
$(document).on('click','.editbtn ',function(event){
    $(this).removeClass().addClass("btn btn-info btn-sm editbtnConfirm");
  var $row = $(this).closest("tr").off("mousedown");
  $row.find("td").not(':first').not(':last').each(function(i, el) {
    var txt = $(this).text();
    $(this).empty().append($('<input>', {
      type : 'text',
      class: "form-control text-center",
      value : txt
    }).data('original-text', txt));
  });
  /*
  var table = $('#waterValuesTable').DataTable();
  var trid = $(this).closest('tr').attr('id');
  // console.log(selectedRow);
  var id = $(this).data('id');
  $('#updateVWModal').modal('show');
  $.ajax({
    url:"waterValuesDirectory/get_single_data.php",
    data:{id:id},
    type:'post',
    success:function(data){
        var json = JSON.parse(data);
        $('#no2Field').val(json.no2);
        $('#no3Field').val(json.no3);
        $('#ghField').val(json.gh);
        $('#khField').val(json.kh);
        $('#po4Field').val(json.po4);
        $('#id').val(id);
        $('#date').val(json.data);
        $('#trid').val(trid);
    }
  })
  */
});

$(document).on('click','.deleteBtn',function(event){
  var table = $('#waterValuesTable').DataTable();
  event.preventDefault();
  var id = $(this).data('id');
  if(confirm("Are you sure want to delete this values?")){
    $.ajax({
      url:"php/waterValuesDirectory/delete_user.php",
      data:{id:id},
      type:"post",
      success:function(data){
        var json = JSON.parse(data);
        status = json.status;
        if(status=='success'){
          //table.fnDeleteRow( table.$('#' + id)[0] );
          $("#waterValuesTable tbody").find(id).remove();
          //table.row($(this).closest("tr")) .remove();
          //$("#"+id).closest('tr').remove();
          table.draw();
        }else{
          alert('Failed');
          return;
        }
      }
    });
  }else{
    return null;
  }
});

$(document).on('click','.addBtn',function(event){
  var table = $('#waterValuesTable').DataTable();
  event.preventDefault();
  var ecP= $('#addecPField').val();
  var ecA= $('#addecAField').val();
  var ph= $('#addphField').val();
  var no2= $('#addNo2Field').val();
  var no3= $('#addNo3Field').val();
  var gh= $('#addGHField').val();
  var kh= $('#addKHField').val();
  var po4= $('#addPo4Field').val();

    $.ajax({
      url:"php/waterValuesDirectory/add_user.php",
      type:"post",
      data:{ecP:ecP, ecA:ecA, ph:ph, no2:no2, no3:no3, gh:gh, kh:kh, po4:po4},
      success:function(data){
        var json = JSON.parse(data);
        var status = json.status;
        if(status=='true'){
          $('#addecPField').val("");
          $('#addecAField').val("");
          $('#addphField').val("");
          $('#addNo2Field').val("");
          $('#addNo3Field').val("");
          $('#addGHField').val("");
          $('#addKHField').val("");
          $('#addPo4Field').val("");
          mytable =$('#waterValuesTable').DataTable();
          mytable.draw();
        }else{
          alert('failed');
        }
      }
    });
  
});

$(document).on('click','.loginbtn',function(event){
  var user = $('#username').val();
  var pass = $('#password').val();

  $.ajax({
    url:"login_db.php",
    data:{username: user, password: pass},
    type:'post',
    success:function(data){
        //alert(user);
    	$('#loginModal').modal('hide');
    }
  })
});

$(document).on('click','.editbtnConfirm',function(event){
	$(this).removeClass().addClass("btn btn-info btn-sm editbtn");
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
      url:"php/waterValuesDirectory/update_user.php",
      type:"post",
      data:{ecP:rowList[0], ecA:rowList[1], ph:rowList[2], no2:rowList[3], no3:rowList[4], gh:rowList[5], kh:rowList[6], po4:rowList[7], id:id},
      success:function(data){   
        var json = JSON.parse(data);
        var status = json.status;
          //alert('Modified');
          console.log(rowList);
      }
    }); 
       
});
