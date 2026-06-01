$(document).ready(function() {
	
  $.datepicker.setDefaults({  
    dateFormat: 'yy-mm-dd'   
  });  
  $(function(){  
    $("#datepicker_ec").datepicker();   
  });  
  $('#filter_ec').click(function(){  
    var selected_date = $('#datepicker_ec').val();   
   
    if(selected_date != '')  
    {  
      $("#table_id").DataTable({
        "destroy": true,
        "bProcessing": true,
        "iDisplayLength": 4,
        "dom": 'rtip',
        "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
          $('td', nRow).css('background-color', 'rgba(33,37,41)');
        },
        "ajax": {
           "url": "php/HistoryValuesIndexPage/fetch_data_ec.php",
           "data": {"d": selected_date},
           "type": "GET"
        },                
        "aoColumns": [{mData: 'id'},
                      {mData: 'data'},
                      {mData: 'ec'}],
        "columnDefs": [{"targets": [0],
                        "visible": false,
                        "searchable": false},
                       {"targets": [1,2], /* column index */
                        "orderable": false /* true or false */}
                      ]    
      });  
    } else {  
      alert("Please Select Date");  
    }  
  });  
     
	$('#table_id').DataTable({
		"bProcessing": true,
        "iDisplayLength": 4,
        "dom": 'rtip',
        "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                        $('td', nRow).css('background-color', 'rgba(33,37,41)');},
        "ajax": {
                  "url": "php/HistoryValuesIndexPage/fetch_data_ec.php",
                  "type": "GET",
                  "data": {"d": "noData"},
                },        
    	"aoColumns": [{mData: 'id'},
                      {mData: 'data'},
                      {mData: 'ec'}],
        "columnDefs": [{"targets": [0],
                        "visible": false,
                        "searchable": false,
                        "orderable": false},
                       {"targets": [1,2], /* column index */
                        "orderable": false /* true or false */}
                      ]    
	});  
         
         
    $(document).on('click','.addConductivity',function(e){
     var table = $('#table_id').DataTable();
     e.preventDefault();
     var ec= $('#addECField').val();
     $.ajax({
       url:"php/HistoryValuesIndexPage/add_ec.php",
       type:"post",
       data:{ec:ec},
       success:function(data){
         var json = JSON.parse(data);
         var status = json.status;
         if(status=='true'){
          $('#addECField').val("");
          $("#table_id").DataTable({
        "destroy": true,
        "bProcessing": true,
        "iDisplayLength": 4,
        "dom": 'rtip',
        "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
          $('td', nRow).css('background-color', 'rgba(33,37,41)');
        },
        "ajax": {
           "url": "php/HistoryValuesIndexPage/fetch_data_ec.php",
           "data": {"d": "noData"},
           "type": "GET"
        },                
        "aoColumns": [{mData: 'id'},
                      {mData: 'data'},
                      {mData: 'ec'}],
        "columnDefs": [{"targets": [0],
                        "visible": false,
                        "searchable": false},
                       {"targets": [1,2], /* column index */
                        "orderable": false,
                        "width": "300%" /* true or false */}
                      ]    
      });  
          //setInterval('location.reload(true)', 1000);
         }else{
           alert('Conductivity adding failed');
         }
       }
     });

   });
   
  $(function(){$("#datepicker_ph").datepicker();});
  
  $('#filter_ph').click(function(){  
    var selected_date = $('#datepicker_ph').val();   
    if(selected_date != '')  
    {  
      $("#ph_history").DataTable({
        "destroy": true,
        "bProcessing": true,
        "iDisplayLength": 4,
        "dom": 'rtip',
        "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
          $('td', nRow).css('background-color', 'rgba(33,37,41)');},
        "ajax": {
           "url": "php/HistoryValuesIndexPage/fetch_data_ph.php",
           "data": {"d": selected_date},
           "type": "GET"
        },      
        "aoColumns": [{mData: 'id'},
                      {mData: 'data'},
                      {mData: 'ph'}],
        "columnDefs": [{"targets": [0],
                        "visible": false,
                        "searchable": false},
                       {"targets": [1,2], /* column index */
                        "orderable": false /* true or false */}
                      ]    
      });  
    } else {  
      alert("Please Select Date");  
    }  
  }); 
  
    $("#ph_history").DataTable({
    	"bProcessing": true,
        "iDisplayLength": 4,
        "dom": 'rtip',
        "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                        $('td', nRow).css('background-color', 'rgba(33,37,41)');},
        "ajax": {
           "url": "php/HistoryValuesIndexPage/fetch_data_ph.php",
           "data": {"d": "noData"},
           "type": "GET"
        },        
        "aoColumns": [{mData: 'id'},
              		  {mData: 'data'},
              		  {mData: 'ph'}],
        "columnDefs": [{"targets": [0],
                        "visible": false,
                        "searchable": false},
                       {"targets": [1,2], /* column index */
                        "orderable": false /* true or false */}]
	});  
      
  
  $(document).on('click', '.addPH',function(e){
      e.preventDefault();
      var ph= $('#addPHFieldP').val();
      $.ajax({
          url:"php/HistoryValuesIndexPage/add_ph.php",
          type:"post",
          data:{ph:ph},
          success:function(data){
            var json = JSON.parse(data);
            var status = json.status;
            if(status=='true'){
                  $('#addPHFieldP').val("");
                  mytables = $("#ph_history").DataTable();
    	          mytables.draw();
                  setInterval('location.reload(true)', 1000);
            }else{
              alert('Ph adding failed');
            }
          }
        }); 
    });
      
  $(function(){$("#datepicker_t").datepicker();});  
  
  $('#filter_t').click(function(){  
    var selected_date = $('#datepicker_t').val();   
    if(selected_date != '')  
    {  
      $("#temperature_history").DataTable({
        "destroy": true,
        "bProcessing": true,
        "iDisplayLength": 4,
        "dom": 'rtip',
        "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
          $('td', nRow).css('background-color', 'rgba(33,37,41)');},
        "ajax": {
           "url": "php/HistoryValuesIndexPage/fetch_data_t.php",
           "data": {"d": selected_date},
           "type": "GET"
        },       
        "sServerMethod": "GET",
                
        "aoColumns": [{mData: 'id'},
                      {mData: 'data'},
                      {mData: 't'}],
        "columnDefs": [{"targets": [0],
                        "visible": false,
                        "searchable": false},
                       {"targets": [1,2], /* column index */
                        "orderable": false /* true or false */}
                      ]    
      });  
    } else {  
      alert("Please Select Date");  
    }  
  }); 
    
    $("#temperature_history").DataTable({
		"bProcessing": true,
        "iDisplayLength": 4,
        "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                        $('td', nRow).css('background-color', 'rgba(33,37,41)');},
        "dom": 'rtip',
        "ajax": {
           "url": "php/HistoryValuesIndexPage/fetch_data_t.php",
           "data": {"d": "noData"},
           "type": "GET"
        },
        "aoColumns": [{mData: 'id'},
              		  {mData: 'data'},
               		  {mData: 't'}],
        "columnDefs": [{"targets": [0],
                        "visible": false,
                        "searchable": false},
                       {"targets": [1,2], /* column index */
                        "orderable": false /* true or false */}]
	});     
    
      $(document).on('click','.addTemperature',function(e){
      e.preventDefault();     
      //var table = $('#temperature_history').DataTable();
      var temp= $('#addTField').val();
      $.ajax({
        url:"php/HistoryValuesIndexPage/add_temperature.php",
        type:"post",
        data:{temp:temp},
        success:function(data){
          var json = JSON.parse(data);
          var status = json.status;
          if(status=='true'){
          
            myTable = $("#temperature_history").DataTable();  
            myTable.draw();
            setInterval('location.reload(true)', 1000);
          }else{
            alert('Temperature adding failed');
          }
        }
      });
    });
    
});
