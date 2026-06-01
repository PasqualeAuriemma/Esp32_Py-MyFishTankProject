$(document).ready(function() {
	
  $.datepicker.setDefaults({  
    dateFormat: 'yy-mm-dd'   
  });
  
   $(function(){  
        $("#datepicker_SettingsP").datepicker();
    });   
        
    $(function(){      
        $("#datepicker_SettingsM").datepicker();
    });
    
    $(function(){  
        $("#datepicker_SettingsI").datepicker();
    });
    
    $(function(){  
        $("#datepicker_SettingsRin").datepicker();
    });
    
    $(function(){  
        $("#datepicker_SettingsPho").datepicker();
    });
    
    $(function(){  
        $("#datepicker_SettingsN").datepicker();
    });
    
    $(function(){  
        $("#datepicker_SettingsS").datepicker();
    });
    
    $(function(){  
        $("#datepicker_SettingsCo2").datepicker();
    });  
    
    $(document).on('submit','#addSettingP',function(e){
      var selected_date = $('#datepicker_SettingsP').val(); 
      var selected_field = $('#addFieldSettingsP').val(); 
      //alert("select: Potassio, data:"+selected_date+", vol:"+selected_field);
      $.ajax({
        url:"php/Volumes/add_volumes.php",
        type:"post",
        data:{select:"Potassio",data:selected_date, vol:selected_field},
        success:function(data){
          var json = JSON.parse(data);
          var status = json.status;
          if(status=='true'){ 
            document.getElementById("addFieldSettingsP")[0].setAttribute("placeholder", selected_field);
            setInterval('location.reload(true)', 1000);
            alert('Complete Adding');
          }else{
            alert('Can\'t insert Date or Volume');
          }
        }
      });
    });  
    
    $(document).on('submit','#addSettingsM', function(e){   
      var selected_date = $('#datepicker_SettingsM').val(); 
      var selected_field = $('#addFieldSettingsM').val(); 
      $.ajax({
        url:"php/Volumes/add_volumes.php",
        type:"post",
        data:{select:"Magnesio",data:selected_date, vol:selected_field},
        success:function(data){
          var json = JSON.parse(data);
          var status = json.status;
          if(status=='true'){   
            setInterval('location.reload(true)', 1000); 
            alert('Complete Adding');
          }else{
            alert('Can\'t insert Date or Volume');
          }
        }
      });
    });
    
    $(document).on('submit','#addSettingsI', function(e){   
       var selected_date = $('#datepicker_SettingsI').val(); 
       var selected_field = $('#addFieldSettingsI').val(); 
       $.ajax({
        url:"php/Volumes/add_volumes.php",
        type:"post",
        data:{select:"Ferro",data:selected_date, vol:selected_field},
        success:function(data){
          var json = JSON.parse(data);
          var status = json.status;
          if(status=='true'){   
            setInterval('location.reload(true)', 1000);
            alert('Complete Adding');
          }else{
            alert('Can\'t insert Date or Volume');
          }
        }
      });
    });
    
    $(document).on('submit','#addSettingsRin', function(e){   
      var selected_date = $('#datepicker_SettingsRin').val(); 
      var selected_field = $('#addFieldSettingsRin').val(); 
      $.ajax({
        url:"php/Volumes/add_volumes.php",
        type:"post",
        data:{select:"Rinverdente",data:selected_date, vol:selected_field},
        success:function(data){
          var json = JSON.parse(data);
          var status = json.status;
          if(status=='true'){   
            setInterval('location.reload(true)', 1000);
            alert('Complete Adding');
          }else{
            alert('Can\'t insert Date or Volume');
          }
        }
      });
    });
     
    $(document).on('submit','#addSettingsPho', function(e){   
       var selected_date = $('#datepicker_SettingsPho').val(); 
       var selected_field = $('#addFieldSettingsPho').val(); 
       $.ajax({
        url:"php/Volumes/add_volumes.php",
        type:"post",
        data:{select:"Fosforo",data:selected_date, vol:selected_field},
        success:function(data){
          var json = JSON.parse(data);
          var status = json.status;
          if(status=='true'){   
            setInterval('location.reload(true)', 1000);
            alert('Complete Adding');
          }else{
            alert('Can\'t insert Date or Volume');
          }
        }
      });
    });
    
    $(document).on('submit','#addSettingsStick', function(e){   
       var selected_date = $('#datepicker_SettingsS').val(); 
       var selected_field = $('#addFieldSettingsS').val(); 
       $.ajax({
        url:"php/Volumes/add_volumes.php",
        type:"post",
        data:{select:"Stick",data:selected_date, vol:selected_field},
        success:function(data){
          var json = JSON.parse(data);
          var status = json.status;
          if(status=='true'){   
            setInterval('location.reload(true)', 1000);
            alert('Complete Adding');
          }else{
            alert('Can\'t insert Date or Volume');
          }
        }
      });
    });
        
    $(document).on('submit','#addSettingsN', function(r){   
       var selected_date = $('#datepicker_SettingsN').val(); 
       var selected_field = $('#addFieldSettingsN').val(); 
       $.ajax({
        url:"php/Volumes/add_volumes.php",
        type:"post",
        data:{select:"Azoto",data:selected_date, vol:selected_field},
        success:function(data){
          var json = JSON.parse(data);
          var status = json.status;
          if(status=='true'){   
             setInterval('location.reload(true)', 1000);
             alert('Complete Adding');
          }else{
            alert('Can\'t insert Date or Volume');
          }
        }
      });
    });
    
    $(document).on('submit','#addSettingsCo2', function(r){   
       var selected_date = $('#datepicker_SettingsCo2').val(); 
       var selected_field = $('#addFieldSettingsCo2').val(); 
       $.ajax({
        url:"php/Volumes/add_volumes.php",
        type:"post",
        data:{select:"Co2",data:selected_date, vol:selected_field},
        success:function(data){
          var json = JSON.parse(data);
          var status = json.status;
          if(status=='true'){   
             setInterval('location.reload(true)', 1000);
             //alert('Complete Adding');
          }else{
            alert('Can\'t insert Date or Volume');
          }
        }
      });
    });
      
});
