$(document).ready(function() {

    $(document).on('submit','#addTemperatureForm', function(e){
      e.preventDefault();                
      var temp= $('#addTFieldP').val();
      $.ajax({
        url:"add_temperature.php",
        type:"post",
        data:{temp:temp},
        success:function(data){
          var json = JSON.parse(data);
          var status = json.status;
          if(status=='true'){
            setInterval('location.reload(true)', 1000);
          }else{
            alert('Temperature adding failed');
          }
        }
      });
    });
   
   	$(document).on('click','.addTemperature',function(e){
      e.preventDefault();     
      var table = $('#temperature_history').DataTable();
      var temp= $('#addTField').val();
      $.ajax({
        url:"add_temperature.php",
        type:"post",
        data:{temp:temp},
        success:function(data){
          var json = JSON.parse(data);
          var status = json.status;
          if(status=='true'){
            table.draw();
            //setInterval('location.reload(true)', 1000);
          }else{
            alert('Temperature adding failed');
          }
        }
      });
    });
    
   $(document).on('submit','#addConductivityForm', function(e){ 
     e.preventDefault();
     var ec= $('#addECFieldP').val();
     $.ajax({
       url:"add_ec.php",
       type:"post",
       data:{ec:ec},
       success:function(data){
         var json = JSON.parse(data);
         var status = json.status;
         if(status=='true'){
           setInterval('location.reload(true)', 1000);
         }else{
           alert('Conductivity adding failed');
         }
       }
     });

   });
   
   $(document).on('click','.addConductivity',function(e){
     e.preventDefault();
     var table = $('#table_id').DataTable();
     var ec= $('#addECField').val();
     $.ajax({
       url:"add_ec.php",
       type:"post",
       data:{ec:ec},
       success:function(data){
         var json = JSON.parse(data);
         var status = json.status;
         if(status=='true'){
           table.draw();
           //setInterval('location.reload(true)', 1000);
         }else{
           alert('Conductivity adding failed');
         }
       }
     });

   });
   
   $(document).on('submit','#addPHForm', function(e){
     e.preventDefault();
      var ph= $('#addPHFieldP').val();
      
      $.ajax({
          url:"add_ph.php",
          type:"post",
          data:{ph:ph},
          success:function(data){
            var json = JSON.parse(data);
            var status = json.status;
            if(status=='true'){
              setInterval('location.reload(true)', 1000);
            }else{
              alert('Ph adding failed');
            }
          }
        }); 
    });
   
   
    $(document).on('click', '.addPH',function(e){
      e.preventDefault();
      var ph= $('#addPHFieldP').val();
      var table = $('#ph_history').DataTable();
      //alert(ph);
      $.ajax({
          url:"add_ph.php",
          type:"post",
          data:{ph:ph},
          success:function(data){
            var json = JSON.parse(data);
            var status = json.status;
            if(status=='true'){
              table.draw();
              //setInterval('location.reload(true)', 1000);
            }else{
              alert('Ph adding failed');
            }
          }
        }); 
    });

       
});
