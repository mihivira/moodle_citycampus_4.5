document.addEventListener("DOMContentLoaded", function() {
    require(['jquery','core/ajax',], function ($,ajax) {
      $( "#id_profile_field_marca" ).click(function() {
        alert( "Si modificas este campo, se vera afectada la relación de unidad operativa y marca" );
      });
      
        informacion_marca_uo = Array();        
        informacion_marca_uo.push({ name: 'request_type', value: 'get_all_marcas_uo' });
        var marcas = [];                          
        
        $.ajax({
            type: "POST",
            url: "services.php",
            data: informacion_marca_uo,
            dataType: "json"
        })
        .done(function(data) {
            console.log('Las marcas-unidades son: ');                      
            var response = data.data; 
         
            marcas = response;                                                                                 
            if (data.status == 'ok') {                
                console.log("Correcto");
                loadMarcaInstitution();                               
            } else { // Se trata de un error
              console.log("Error");
            }
        })
        .fail(function(error, error2) {            
            console.log(error, error2);
        });        

        var hotel = $("#id_institution").val();     
                  
        if(isGG){
          $("#id_profile_field_marca option[value='"+marca+"']").attr('selected',true);
        } else {
          changeMarca(hotel);
        }
        
        var puestomarca = $("#id_department").val() + " - " + $("#id_profile_field_marca").val();
        $("#id_profile_field_Puestomarca").val(puestomarca);       


        $("select").change(function(){
          var puestomarca = $("#id_department").val() + " - " + $("#id_profile_field_marca").val();
          $("#id_profile_field_Puestomarca").val(puestomarca);
        });

        function loadMarcaInstitution(){
          console.log("FUNCION LOAD");          
          var hotel = $("#id_institution").val();
          console.log(hotel);
          changeMarca(hotel);      
          reloadInstitutions(hotel);    
        }

        $("#id_institution").change(function(){
            $('#id_profile_field_marca option:selected').attr('selected', false);
            var hotel = $("#id_institution").val();     
            console.log(hotel);
            changeMarca(hotel);
            reloadInstitutions(hotel);

        });

        function changeMarca(hotel){
          $("#id_profile_field_marca").find('option:selected').removeAttr("selected");
          console.log("changeMarca");
          console.log(hotel);
          console.log(marcas);
          marcas.forEach(marca => {           
            if(marca.value.indexOf(hotel) != -1){
              console.log("se encontro en " + marca.name);
              $("#id_profile_field_marca option[value='" + marca.name + "']").attr('selected',true);  
              var puestomarca = $("#id_department").val() + " - " + $("#id_profile_field_marca").val();
              $("#id_profile_field_Puestomarca").val(puestomarca);            
            }else{
              "No se encontro";
            }            
          });
          
        }

        function reloadInstitutions(institution){
          console.log("Entra a reloadInstitutions");
          ajax.call([{
            methodname: 'local_hoteles_city_dashboard_get_institutions_ggc',
            args: {
              data: {
                  'institution': institution,
              },
            },
          }])[0]
          .done(function(response) {
            var data = JSON.parse(response);
            var select = document.getElementById("id_department");
            var puestoUser = select.value;
            console.log('Puesto user');
            console.log(puestoUser);
            // Limpiar el select de opciones existentes
            select.innerHTML = '';

          // Agregar las nuevas opciones según los datos recibidos en la respuesta AJAX
          for (var key in data) {
              if (data.hasOwnProperty(key)) {
                  var optionElement = document.createElement("option");
                  optionElement.value = key;
                  optionElement.textContent = data[key];
                  select.appendChild(optionElement);
              }
          }

          select.value = puestoUser;



          });

        }

    });
});
