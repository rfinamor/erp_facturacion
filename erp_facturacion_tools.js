$(document).ready(function() {

    init();
    initDatePicker();

    function init() {
        getUsuarios(function(users) {
            buildSelect('selectUsuarios','divUsuario',users);
        });
        getTiposComprobante(function(tipos) {
            buildSelectTipos('selectTipos','divTipos',tipos);
        });

        $("#datosFactura").hide();
        $("#datosFacturaTalonario").hide();
        $("#datosFacturaNumero").hide();
        $("#datosFacturaUsuario").hide();
        $("#datosFacturaFecha").hide();

    }

    let detailsData;

    function initdetailsData() {
        detailsData = {
            "details": [],
        }
    }

        getCodigoArticulo(function() {
            initdetailsData();
            renderDetails(detailsData);
        });

    let detailCodigoArticulo = [];

    function getCodigoArticulo(callback) {
        $.ajax({
            url: "erp_facturacion_tools_ajax.php",
            type: "POST",
            dataType: 'JSON',
            data: {
                funcion: "getCodigoArticulo"
            },
            success: function(codigo) {
                console.log(codigo);
                if(callback) {
                    callback(codigo);
                }
                codigo.forEach(function(codigo) {
                    detailCodigoArticulo.push(codigo);
                })
            }

        });
    }

    function addDetail() {
        detailsData.details.push({
            "detalle": "",
            "cantidad": "",
            "precio": "",
            "codigo": "",
            "alicuota": "",
        });
        renderDetails(detailsData);
    }

    function removeDetail(index) {
        detailsData.details.splice(index, 1);
        renderDetails(detailsData);
    }

    const container = $('#container');
    const addBtn = $('#add');
    const ingresarBtn = $('#btnIngresarFactura');

    ingresarBtn.click(function() {
        //Aca voy a guardar
        console.log(detailsData);
    });

    addBtn.click(function() {
        addDetail();
    });

    $('#container').on('click', '.removeBtn', function() {
        removeDetail($(this).parent().index());
    })

    function createNewDetail(detalle, cantidad, precio, codigo, alicuota) {
        const newDetail = $('<div>', {
            "class": 'detail control-group col-sm-3 input-prepend',
        });

        const detailDescripcionInput = $('<input>', {
            type: 'text',
            placeholder: 'Descripcion',
            "class": 'detalle form-control inputDatos inputDetalle',
            value: detalle,
        });

        detailDescripcionInput.keyup(function(e) {
            const index = $(this).parent().index();
            detailsData.details[index].detalle = e.target.value;
        });

        const detailCantidadInput = $('<input>', {
            type: 'number',
            placeholder: 'Cantidad',
            "class": 'cantidad form-control inputDatos inputDetalle',
            value: cantidad,
        });

        detailCantidadInput.keyup(function(e) {
            const index = $(this).parent().index();
            detailsData.details[index].cantidad = e.target.value;
        });

        const detailPrecioInput = $('<input>', {
            type: 'number',
            placeholder: 'Precio',
            "class": 'precio form-control inputDatos inputDetalle',
            value: precio,
        });

        detailPrecioInput.keyup(function(e) {
            const index = $(this).parent().index();
            detailsData.details[index].precio = e.target.value;
        });

        const detailAlicuotaSelect = $('<select>', {
            "class": 'form-control inputDatos inputDetalle',
            value: codigo.codigo,
            text:"Seleccionar Alicuota",
        });

        const AlicuotaOptionDefault = $('<option>', {
            value: '',
            text: 'Seleccionar Alicuota',
        });

        const AlicuotaOption1 = $('<option>', {
            value: 0,
            text: 'No gravado',
        });

        const AlicuotaOption2 = $('<option>', {
            value: 1,
            text: 'IVA 21%',
        });

        detailAlicuotaSelect.append(AlicuotaOptionDefault);
        detailAlicuotaSelect.append(AlicuotaOption1);
        detailAlicuotaSelect.append(AlicuotaOption2);

        detailAlicuotaSelect.change(function(e){
            const index = $(this).parent().index();
            detailsData.details[index].alicuota = e.target.value;
        });

        const detailCodigoSelect = $('<select>', {
            "class": 'form-control inputDatos inputDetalle',
            text: "Seleccionar codigo",
            value: "",
        });

        const codigoDefault = $('<option>', {
            value: '',
            text: 'Seleccionar Codigo Articulo',
        });

        detailCodigoSelect.append(codigoDefault);

        detailCodigoArticulo.forEach(function(codigo) {
            const codigoOptions = $('<option>', {
                value: codigo.codigo,
                text: codigo.codigo,
            });
            detailCodigoSelect.append(codigoOptions);
        });


        detailCodigoSelect.change(function(e){
            const index = $(this).parent().index();
            detailsData.details[index].codigo = e.target.value;
        });

        const removeBtn = $('<button>', {
            "class": 'removeBtn btn btn-danger',
        });

        removeBtn.text("Eliminar")

        newDetail.append(detailCodigoSelect);
        newDetail.append(detailAlicuotaSelect);
        newDetail.append(detailDescripcionInput);
        newDetail.append(detailCantidadInput);
        newDetail.append(detailPrecioInput);
        newDetail.append(removeBtn);

        return newDetail;
    }

    function renderDetails(detailsData) {
        container.empty();
        detailsData.details.forEach(function(detail) {
            const newDetailContainer = createNewDetail(detail.detalle, detail.cantidad, detail.precio, detail.codigo, detail.alicuota);
            container.append(newDetailContainer);
        });
    }



    $('#datepicker').change(function(){
        $('#datepicker').datepicker('hide');
    });

    function initDatePicker(){
        $('#datepicker').datepicker({
            format: "yyyy-mm-dd",
            language: "es",
            multidate: false
        }).on('change', function(){
            $('.datepicker').hide();
        });

        $('#datepicker2').datepicker({
            format: "yyyy-mm-dd",
            language: "es",
            multidate: false
        }).on('change', function(){
            $('.datepicker2').hide();
        });

        $('#datepicker3').datepicker({
            format: "yyyy-mm-dd",
            language: "es",
            multidate: false
        }).on('change', function(){
            $('.datepicker3').hide();
        });

        $('#datepicker4').datepicker({
            format: "yyyy-mm-dd",
            language: "es",
            multidate: false
        }).on('change', function(){
            $('.datepicker4').hide();
        });

        $('#datepicker5 ').datepicker({
            format: "yyyy-mm-dd",
            language: "es",
            multidate: false
        }).on('change', function(){
            $('.datepicker5').hide();
        });

        $('#datepicker').datepicker('setDate', new Date());
        $('#datepicker2').datepicker('setDate', new Date());
        $('#datepicker3').datepicker('setDate', new Date());
        $('#datepicker4').datepicker('setDate', new Date());
        $('#datepicker5').datepicker('setDate', new Date());
    }



    // Guardar
    $("#btnIngresarFactura").click(function(){

        //Tomo valores erp_comprobantes
        var id_usuario_selected = $(".selectUsuario").val();
        var id_sociedad = $(".selectUsuario").find(':selected').data('sociedad');
        var tipos = $(".selectTipos").val();
        var prefijo = $("#prefijo").val();
        var talonario = $("#talonario").val();
        var numero_factura = $("#numeroFactura").val();
        var cae = $("#cae").val();
        var vencimiento_cae = jQuery('#datepicker3').datepicker('getFormattedDate');
        var fecha_emision = $("#datepicker").datepicker('getFormattedDate');
        var fecha_vencimiento = $("#datepicker2").datepicker('getFormattedDate');
        var fecha_desde = $("#datepicker4").datepicker('getFormattedDate');
        var fecha_hasta = $("#datepicker5").datepicker('getFormattedDate');

        //tomo valores erp_comprobantes_detalles
        dataDetails = JSON.stringify(detailsData);

        $.ajax({
            url: "erp_facturacion_tools_ajax.php",
            type: "POST",
            dataType: 'JSON',
            data: {
                funcion: "ingresarFactura",
                //erp_comprobantes
                id_usuario_selected: id_usuario_selected,
                id_sociedad: id_sociedad,
                tipos: tipos,
                prefijo: prefijo,
                talonario: talonario,
                numero_factura: numero_factura,
                cae: cae,
                vencimiento_cae: vencimiento_cae,
                fecha_emision: fecha_emision,
                fecha_vencimiento: fecha_vencimiento,
                fecha_desde: fecha_desde,
                fecha_hasta: fecha_hasta,
                //erp_comprobantes_detalles
                detail : dataDetails
            },
            success: function(data){

                if(typeof data.result === 'boolean' && data.result === true) {
                    $("#noti_succ").text("Factura Guardada!");
                    $("#noti_succ").show();
                    setTimeout(function(){
                        $("#noti_succ").hide();
                    }, 5000);
                    limpiarCampos();
                    setTimeout(function(){
                        location.reload();
                    }, 5200);

                } else {
                    document.getElementById("noti_error").innerHTML = data.error;
                    $("#noti_error").show();
                    setTimeout(function(){
                        $("#noti_error").hide();
                    }, 9000);
                }

            }

        });

    });

    $("#btnIngresarCC").click(function(){

        //Tomo valores erp_comprobantes
        var cuenta_contable = $("#cuentaContable").val();
        var descripcion_cuenta = $("#descripcionCuenta").val();
        var imputable = $("#selectImputable").val();
        var moneda = $("#selectMoneda").val();


        $.ajax({
            url: "erp_facturacion_tools_ajax.php",
            type: "POST",
            dataType: 'JSON',
            data: {
                funcion: "ingresarCuentaContable",
                //erp_comprobantes
                cuenta_contable: cuenta_contable,
                descripcion_cuenta: descripcion_cuenta,
                imputable: imputable,
                moneda: moneda
            },
            success: function(data){

                if(typeof data.result === 'boolean' && data.result === true) {
                    $("#noti_CCsucc").text("Cuenta Contable Guardada!");
                    $("#noti_CCsucc").show();
                    setTimeout(function(){
                        $("#noti_CCsucc").hide();
                    }, 3000);
                    limpiarCampos();
                } else {
                    $("#noti_CCError").text(data.error);
                    $("#noti_CCError").show();
                    setTimeout(function(){
                        $("#noti_CCError").hide();
                    }, 5000);
                }

            }

        });

    });




    $("#btnBuscarFactura").click(function(){

        //tomo valores para la busqueda de factura
        var talonario_busqueda = $("#talonarioBusqueda").val();
        var factura_busqueda = $("#numeroFacturaBusqueda").val();
        var prefijo = $("#prefijoEliminar").val();
        var tipos = $("#tipo").val();



        $.ajax({
            url: "erp_facturacion_tools_ajax.php",
            type: "POST",
            dataType: 'JSON',
            data: {
                funcion: "buscarFacturaEliminar",
                //busqueda factura
                talonario_busqueda: talonario_busqueda,
                factura_busqueda: factura_busqueda,
                tipos: tipos,
                prefijo: prefijo,
            },
            success: function(data){

                var codigo_usuario = data.codigo_usuario;
                var tipo = data.tipo;
                var prefijo = data.prefijo;
                var talonario = data.talonario;
                var numero_factura = data.factura;
                var fecha_emision = data.fecha_emision;
                var id_comprobante = data.id_comprobante;

                if(typeof data.result === 'boolean' && data.result === true) {
                    $("#datosFactura").show();
                    $("#datosTipoFactura").show();
                    $("#datosPrefijo").show();
                    $("#datosFacturaTalonario").show();
                    $("#datosFacturaNumero").show();
                    $("#datosFacturaUsuario").show();
                    $("#datosFacturaFecha").show();

                    $("#datosFacturaUsuario").text("Usuario: "+codigo_usuario);
                    $("#datosTipoFactura").text("Tipo: "+tipo);
                    $("#datosPrefijo").text("Prefijo: "+prefijo);
                    $("#datosFacturaTalonario").text("Talonario: 0"+talonario);
                    $("#datosFacturaNumero").text("Numero: "+numero_factura);
                    $("#datosFacturaFecha").text("Fecha Emision: "+fecha_emision);
                    $("#idComprobante").val(id_comprobante);

                    //botonshow

                } else {
                    $("#noti_facBorradoError").text(data.error);
                    $("#noti_facBorradoError").show();
                    setTimeout(function(){
                        $("#datosFactura").hide();//esconder campos
                        $("#noti_facBorradoError").hide();
                    }, 5000);
                }

            }

        });

    });

    $("#btnEliminarFactura").click(function(){

        //Tomo valores erp_comprobantes
        var id_comprobante = $("#idComprobante").val();
        var talonario_busqueda = $("#talonarioBusqueda").val();
        var factura_busqueda = $("#numeroFacturaBusqueda").val();
        var prefijo = $("#prefijoEliminar").val();
        var tipos = $("#tipo").val();


        $.ajax({
            url: "erp_facturacion_tools_ajax.php",
            type: "POST",
            dataType: 'JSON',
            data: {
                funcion: "eliminarFactura",
                //datos para eliminar
                id_comprobante: id_comprobante,
                talonario_busqueda:talonario_busqueda,
                factura_busqueda:factura_busqueda,
                tipos: tipos,
                prefijo: prefijo,

            },
            success: function(data){

                if(typeof data.result === 'boolean' && data.result === true) {
                    $("#datosFactura").hide();
                    $("#noti_facBorradosucc").text("Factura Eliminada!");
                    $("#noti_facBorradosucc").show();
                    setTimeout(function(){
                        $("#noti_facBorradosucc").hide();
                    }, 3000);
                    limpiarCampos();
                } else {
                    console.log(data.error);
                    $("#noti_facBorradoError").text(data.error);
                    $("#noti_facBorradoError").show();
                    setTimeout(function(){
                        $("#noti_facBorradoError").hide();
                    }, 5000);
                }

            }

        });

    });


});
//Fin del document ready.




//getters

function getUsuarios(callback) {

    $.ajax({
        url: "erp_facturacion_tools_ajax.php",
        type: "POST",
        dataType: 'JSON',
        data: {
            funcion: "getUsuarios"
        },
        success: function(users) {
            console.log(users);
            if(callback) {
                callback(users);
            }
        }

    });

}

function getTiposComprobante(callback) {

    $.ajax({
        url: "erp_facturacion_tools_ajax.php",
        type: "POST",
        dataType: 'JSON',
        data: {
            funcion: "getTiposComprobante"
        },
        success: function(tipos) {
            if(callback) {
                callback(tipos);
            }
        }

    });

}

function getCodigoArticulo(callback) {

    $.ajax({
        url: "erp_facturacion_tools_ajax.php",
        type: "POST",
        dataType: 'JSON',
        data: {
            funcion: "getCodigoArticulo"
        },
        success: function(codigos) {
            if(callback) {
                callback(codigos);
            }
        }

    });

}


// Selects
function buildSelect(selectId, selectContainer, users) {
    var select = $('<select id='+selectId+' class="selectUsuario">').addClass('form-control inputDatos');

    //Default option
    var option = $('<option>')
        .text('Seleccionar Usuario')
        .attr('value','');
    select.append(option);

    for (var i = 0; i < users.length; i++) {
        var user = users[i];


        var option = $('<option>')
            .val(user.id)
            .data("sociedad", user.id_sociedad)
            .text(user.codigo);

        select.append(option);
    }

    $("#" + selectContainer).html(select);

    var warningCliente = $("<div>",{
        class : "alert alert-warning ",
        id : "es-cliente-warning",
        style : "display:none"
    }).append("<strong>Atencion!</strong> Usted ha seleccionado un usuario que NO es cliente.");

    select.after(warningCliente);
}


function buildSelectTipos(selectId, selectContainer, tipos) {
    var select = $('<select id='+selectId+' class="selectTipos">').addClass('form-control inputDatos');

    //Default option
    var option = $('<option>')
        .text('Seleccionar Tipo Comprobante')
        .attr('value','');
    select.append(option);

    for (var i = 0; i < tipos.length; i++) {
        var tipo = tipos[i];

        var option = $('<option>')
            .val(tipo.tipo_comprobante).data("Tipo", tipo.tipo_comprobante)
            .text(tipo.tipo_comprobante);

        select.append(option);
    }

    $("#" + selectContainer).html("").append(select);


}

//funciones limpieza

function limpiarCampos(){

    $(".selectUsuario").val("");
    $(".selectTipos").val("");
    $("#prefijo").val("");
    $("#talonario").val("");
    $("#numeroFactura").val("");
    $("#cae").val("");
    $('#datepicker3').datepicker('setDate', new Date());
    $("#datepicker").datepicker('setDate', new Date());
    $("#datepicker2").datepicker('setDate', new Date());
    $("#datepicker4").datepicker('setDate', new Date());
    $("#datepicker5").datepicker('setDate', new Date());

    $(".selectCodigo").val("");
    $(".selectAlicuota").val("");
    $("#cantidad").val("");
    $("#precio").val("");
    $("#descripcion").val("");

    $("#cuentaContable").val("");
    $("#descripcionCuenta").val("");
    $("#selectImputable").val("");
    $("#selectMoneda").val("");

    $("#talonarioBusqueda").val("");
    $("#numeroFacturaBusqueda").val("");

    $('#container').remove();

}

