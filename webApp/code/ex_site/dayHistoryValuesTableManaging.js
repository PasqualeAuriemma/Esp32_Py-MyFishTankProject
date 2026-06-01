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
           "url": "fetch_data_ec.php",
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
     
	$("#table_id").DataTable({
		"bProcessing": true,
        "iDisplayLength": 4,
        "dom": 'rtip',
        "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                        $('td', nRow).css('background-color', 'rgba(33,37,41)');},
        "ajax": {
                  "url": "fetch_data_ec.php",
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
         
     $(function(){  
    $("#datepicker_ph").datepicker();   
  });  
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
           "url": "fetch_data_ph.php",
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
           "url": "fetch_data_ph.php",
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
      
      
  $(function(){  
    $("#datepicker_t").datepicker();   
  });  
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
           "url": "fetch_data_t.php",
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
           "url": "fetch_data_t.php",
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
});
