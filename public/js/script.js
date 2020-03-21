/**
 * Horse Race Simulator
 *
 * @category   Application_Core
 * @package    horse-race-simulator
 * @author     Suman Barua
 * @developer  Suman Barua <sumanbarua576@gmail.com>
 */

$(document).ready(function() {
    // check can progress onload or not
    if($canProgress) {
        $('#progressRace').removeAttr("disabled");
    }
    
    // Trigger on create button is clicked
    $('#createRace').click(function(event) { 
        event.preventDefault(); // Prevent the default operation

        // check can progress or not?
        if($(this).is(":disabled")) {
            return null;
        }

        // make ajax call
        var request = $.ajax({
          url: $appBaseUrl + 'race/createrace',
          method: "GET",
          dataType: "json"
        });

        // request success
        request.done(function(data) {
          if(!data.canCreate){
              $('#createRace').attr("disabled", "disabled");
          }else{
              $('#createRace').removeAttr("disabled");
          }  

          if(!data.canProgress){
              $('#progressRace').attr("disabled", "disabled");
          }else{
              $('#progressRace').removeAttr("disabled");
          } 
          $('#bodyContents').empty();
          $("#bodyContents").html(data.htmlContent);
        });

        // request failed
        request.fail(function(jqXHR, textStatus) {
          alert("Request failed: " + textStatus);
        }); 
    });
    
    // Trigger on progress button is clicked
    $('#progressRace').click(function(event) { 
        event.preventDefault(); // Prevent the default operation

        // check can progress or not?
        if($(this).is(":disabled")) {
            return null;
        }
        
        // make ajax call
        var request = $.ajax({
          url: $appBaseUrl + 'race/progress',
          method: "GET",
          dataType: "json"
        });

        // request success
        request.done(function(data) {
          if(!data.canCreate){
              $('#createRace').attr("disabled", "disabled");
          }else{
              $('#createRace').removeAttr("disabled");
          }  

          if(!data.canProgress){
              $('#progressRace').attr("disabled", "disabled");
          }else{
              $('#progressRace').removeAttr("disabled");
          } 
          $('#bodyContents').empty();
          $("#bodyContents").html(data.htmlContent);
        });

        // request failed
        request.fail(function(jqXHR, textStatus) {
          alert("Request failed: " + textStatus);
        }); 
    });
});    