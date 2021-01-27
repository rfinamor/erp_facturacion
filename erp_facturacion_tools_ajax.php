<?php
/**
 * Created by PhpStorm.
 * User: Rodrigo Finamore
 * Date: 29/6/2020
 * Time: 22:02
 */

include_once('../util_db.php');
include_once('../util_session.php');

manejar_sesion();


$usuario_id = $_SESSION['usuario_id'];

$permisos = get_permisos($usuario_id, basename($_SERVER['SCRIPT_NAME']));
if (!in_array('ACCESO', $permisos)) die ("No autorizado");



$funcion = _post("funcion");

$response = '';

if($funcion){
    switch($funcion) {
        case 'ingresarFactura':
            $response = ingresarFactura();
            break;
        case 'ingresarCuentaContable':
            $response = ingresarCuentaContable();
            break;
        case 'getUsuarios':
            $response = getUsuarios();
            break;
        case 'getTiposComprobante':
            $response = getTiposComprobante();
            break;
        case 'getCodigoArticulo':
            $response = getCodigoArticulo();
            break;
        case 'buscarFacturaEliminar':
            $response = buscarFacturaEliminar();
            break;
        case 'eliminarFactura':
            $response = eliminarFactura();
            break;
    }

}
echo json_encode($response);


Function getUsuarios(){

    $usuarios = array();

    $r_usuarios = mysql_query("SELECT id,nombre as codigo,id_sociedad FROM usuarios WHERE borrado_fecha IS NULL AND borrado_usuario_id IS NULL and codigo NOT LIKE 'proveedores@%' AND id_sociedad = 1  AND es_cliente = 1 ORDER BY codigo");

    while ($row = mysql_fetch_assoc($r_usuarios)){
        $usuarios[] = $row;
    }

    return $usuarios;
}

function getTiposComprobante() {
    $result = mysql_query("SELECT DISTINCT tipo_comprobante FROM erp_comprobantes WHERE tipo_comprobante in ('FAC','N/C','N/CE','N/D','N/DE') ORDER BY tipo_comprobante");
    $valores = array();
    while($row = mysql_fetch_assoc($result)) {
        $valores[] = $row;
    }
    return $valores;

}

function getCodigoArticulo() {
    $result = mysql_query("SELECT DISTINCT codigo FROM erp_articulos WHERE id_sociedad = 1 AND anulado = 0 ORDER BY codigo ASC");
    $valores = array();
    while($row = mysql_fetch_assoc($result)) {
        $valores[] = $row;
    }
    return $valores;

}

function getAlicuota() {
    $result = mysql_query("SELECT id,descripcion FROM alicuotas_iva");
    $valores = array();
    while($row = mysql_fetch_assoc($result)) {
        $valores[] = $row;
    }
    return $valores;

}

function getFactura($tipo, $prefijo, $talonario,$numero){
    $sql = "SELECT ec.id,ec.talonario,ec.numero,ec.fecha_emision,u.codigo, ec.tipo_comprobante, ec.prefijo
            FROM erp_comprobantes ec
            INNER JOIN usuarios u ON u.id = ec.id_usuario
            WHERE ec.talonario = '$talonario' AND ec.numero = '$numero' AND ec.prefijo = '$prefijo' AND ec.tipo_comprobante = '$tipo'";


    $query = mysql_query($sql);

    if($busqueda = mysql_fetch_assoc($query)){
        $factura = array(
            'result' => true,
            'id_comprobante' => $busqueda['id'],
            'tipo' => $busqueda['tipo_comprobante'],
            'prefijo' => $busqueda['prefijo'],
            'talonario' => $busqueda['talonario'],
            'factura' => $busqueda['numero'],
            'fecha_emision' => $busqueda['fecha_emision'],
            'codigo_usuario' => $busqueda['codigo']);
        return $factura;

    }else{
        $factura = array('result' => false,'error' => 'No se encontraron facturas.');
        return $factura;
    }
}




function ingresarFactura(){
    $usuario_id = $_SESSION['usuario_id'];

    $result = array();
    $resultDetalle = array();
    $error = '';
    //Recibo datos de erp_comprobantes
    $result['id_usuario_selected']  =trim( _post("id_usuario_selected")," ");
    $result['id_sociedad'] =trim( _post("id_sociedad")," ");
    $result['tipos'] =trim( _post("tipos")," ");
    $result['prefijo'] =strtoupper(trim( _post("prefijo")," "));
    $result['talonario'] =substr(trim( _post("talonario")," "),-4);

    $result['numero_factura'] =trim( _post("numero_factura")," ");
    $result['cae'] =trim( _post("cae")," ");
    $result['vencimiento_cae'] =trim( _post("vencimiento_cae")," ");
    $result['fecha_emision'] =trim( _post("fecha_emision")," ");
    $result['fecha_vencimiento'] =trim( _post("fecha_vencimiento")," ");
    $result['fecha_desde'] =trim( _post("fecha_desde")," ");
    $result['fecha_hasta'] =trim( _post("fecha_hasta")," ");

    //recibo datos de erp:comprobantes_detalles
    $resultado_detalle= json_decode($_POST['detail'],true);

    $resultado = array('result' => false, 'error' => 'Error, no se pudo cargar la factura');


    $sqlInsertDetalle = '';
    $last_insert = '';

    $factura = getFactura($result['tipos'],$result['prefijo'],$result['talonario'],$result['numero_factura']);
    $factura_cc = "";
    if($factura['result'] === true){
        $error.= "La factura ".$result['tipos'] ." " .$result['prefijo'] ." " ._post("talonario") ."-" .$result['numero_factura'] ." del Usuario " .$factura['codigo_usuario'] ." ya existe.";
        $resultado = array('result' => false, 'error' => $error);
        return $resultado;
    }
    else {
        $factura_cc .= $result['talonario']."-" .$result['numero_factura'];
        $sql_cc = "SELECT id,id_comprobante FROM usuarios_cc WHERE id_usuario = '$result[id_usuario_selected]' AND comprobante like '%$factura_cc%'";
        $query_cc = mysql_query($sql_cc);
        if($usuarios_cc = mysql_fetch_assoc($query_cc)){
            $id_usuarios_cc = $usuarios_cc['id'];
            $id_comprobante_cc = $usuarios_cc['id_comprobante'];
        }

        if ($id_usuarios_cc != "" && $id_usuarios_cc != null && $id_comprobante_cc != "" && $id_comprobante_cc != null) {
            $error.= "La factura ".$result['tipos'] ." " .$result['prefijo'] ." " ._post("talonario") ."-" .$result['numero_factura'] ." del Usuario " .$factura['codigo_usuario'] ." ya posee un comprobante cargado.";
            $resultado = array('result' => false, 'error' => $error);
            return $resultado;

        } else if (!$id_usuarios_cc) {
            $error .= "La factura " . $result['tipos'] . " " . $result['prefijo'] . " " . _post("talonario") . "-" . $result['numero_factura'] . " del Usuario " . $factura['codigo_usuario'] . " debe ser cargada en la cuenta corriente.";
            $resultado = array('result' => false, 'error' => $error);
            return $resultado;

        } else if ($id_usuarios_cc != "" && $id_usuarios_cc != null && !$id_comprobante_cc){

            if ($resultado_detalle['details'] == '' || $resultado_detalle['details'] == null) {
                $error .= "Falta cargar el detalle de la factura";
            }

            if (strtotime($result['fecha_desde']) > strtotime($result['fecha_hasta'])) {
                $error .= "<br/>La fecha desde no puede ser mayor que fecha hasta.";
            }

            if (strtotime($result['fecha_emision']) > strtotime($result['fecha_vencimiento'])) {
                $error .= "<br/>La fecha de emision no puede ser mayor que la fecha de vencimiento.";
            }


            foreach ($resultado_detalle['details'] as $indice => $detalle) {

                $codigo = trim($detalle['codigo'], " ");
                $precio = trim($detalle['precio'], " ");
                $descripcion = trim($detalle['detalle'], " ");
                $cantidad = trim($detalle['cantidad'], " ");
                $alicuota = trim($detalle['alicuota'], " ");

                $indiceError = $indice + 1;

                if ($codigo === null || $codigo === '' || $codigo === '0') {
                    $error .= 'Falta seleccionar el codigo en el detalle numero ' . $indiceError;
                }

                if ($precio === null || $precio === '' || $precio === '0') {
                    $error .= '<br/>Falta completar el precio en el detalle numero ' . $indiceError;
                }

                if ($descripcion === null || $descripcion === '' || $descripcion === '0') {
                    $error .= '<br/>Falta completar la descripcion en el detalle numero ' . $indiceError;
                }

                if ($cantidad === null || $cantidad === '' || $cantidad === '0') {
                    $error .= '<br/>Falta completar la cantidad en el detalle numero ' . $indiceError;
                }

                if ($alicuota === null || $alicuota === '') {
                    $error .= '<br/>Falta seleccionar la alicuota en el detalle numero ' . $indiceError;
                }

                if($result['tipos'] === 'N/C' || $result['tipos'] === 'N/CE'){
                    $codigo = 'N/C';
                }

                if ($indice > 0) {
                    $sqlInsertDetalle .= ",(" . $last_insert . ",'" . $codigo . "','" . $cantidad . "','" . $precio . "','" . $descripcion . "', null, null, null, null,'" . $alicuota . "')";
                } else {

                    $evaluoError = array($result['id_usuario_selected'], $result['id_sociedad'], $result['tipos'], $result['prefijo'], $result['talonario'], $result['numero_factura'], $result['cae'],
                        $result['vencimiento_cae'], $result['fecha_emision'], $result['fecha_vencimiento'], $result['fecha_desde'], $result['fecha_hasta']);


                    for ($i = 0; $i < count($evaluoError); $i++) {
                        if ($evaluoError[$i] === null || $evaluoError[$i] === '' || $evaluoError[$i] === '0') {
                            $error .= "<br/>Falta completar algun campo de la primer seccion de la factura";
                            break;
                        }
                    }

                    $resultado = array('result' => false, 'error' => $error);


                    if ($error == '' || $error == false || !$error) {

                        //Inserto en erp comprobante

                        $sql = "INSERT INTO saas.erp_comprobantes (prefijo, talonario, numero, tipo_comprobante, id_usuario, fecha_emision, fecha_vencimiento, cae, cae_vencimiento,
                                           guid, fecha_desde, fecha_hasta, id_usuario_creacion, fecha_creacion,
                                           id_sociedad, id_pedido, id_instalador, comentarios, id_ot, procesado_operaciones,
                                           ticket_pedido, id_usuario_baja, fecha_baja, id_ctacte, anulado) 
                                           VALUES
                                          ('$result[prefijo]', '$result[talonario]', '$result[numero_factura]', '$result[tipos]', $result[id_usuario_selected],
                                           '$result[fecha_emision]', '$result[fecha_vencimiento]', '$result[cae]', '$result[vencimiento_cae]', '',
                                          '$result[fecha_desde]', '$result[fecha_hasta]', $usuario_id, CURDATE(), '$result[id_sociedad]', null, null, null, null, 0, null, null, null, null, 0)";
                        mysql_query($sql);

                        $last_insert = mysql_insert_id();

                        $sqlInsertDetalle .= "(" . $last_insert . ",'" . $codigo . "','" . $cantidad . "','" . $precio . "','" . $descripcion . "', null, null, null, null,'" . $alicuota . "')";




                    } else {
                        return $resultado;
                    }
                }

            }

            $sqlDetalle = "INSERT INTO erp_comprobantes_detalles (id_comprobante, codigo_articulo, cantidad, precio, descripcion, id_erp_ctacte, fecha, detalle, url, id_alicuota) 
                          VALUES $sqlInsertDetalle";

            $sql_cc_upd = "UPDATE usuarios_cc SET id_comprobante = $last_insert WHERE id = $id_usuarios_cc";
            mysql_query($sql_cc_upd);

            mysql_query($sqlDetalle);

            loggear("Se ingresó nueva factura " . $result . $sqlInsertDetalle, "erp_facturacion_tools");


            $resultado = array('result' => true, 'error' => 'ok');


        }
    }
    return $resultado;

}

function ingresarCuentaContable(){

    $result = array();
    $error = '';

    //Recibo datos de erp_contab_cuentas
    $result['cuenta_contable']  = trim(_post("cuenta_contable")," ");
    $result['descripcion_cuenta'] =trim( _post("descripcion_cuenta")," ");
    $result['imputable'] = _post("imputable");
    $result['moneda'] = _post("moneda");

    $query = mysql_query("SELECT id FROM erp_contab_cuentas WHERE id = '$result[cuenta_contable]'");

    if($r = mysql_fetch_assoc($query)){
        $resultado = array('result' => false,'error' => 'El id de la cuenta contable ingresada ya existe');
        return $resultado;
    }else{

        $evaluoError = array($result['cuenta_contable'],$result['descripcion_cuenta'],$result['imputable'],$result['moneda']);


        for($i = 0; $i < count($evaluoError); $i++){
            if($evaluoError[$i]=== null || $evaluoError[$i]=== '' || $evaluoError[$i] === 0 ){
                $error= 'Falta completar algun campo';
            }
        }




        $resultado = array('result' => false, 'error' => $error);




        if($error == '' || $error == false || !$error){

            //Inserto en erp_contab_cuentas

            $sql = "INSERT INTO erp_contab_cuentas (id, descripcion, imputable, moneda) VALUES ('$result[cuenta_contable]','$result[descripcion_cuenta]',$result[imputable],'$result[moneda]')";
            mysql_query($sql);


            loggear("Se ingresó cuenta contable ".$result,"erp_facturacion_tools");


            $resultado = array('result' => true,'error' => 'ok');

        }


        return $resultado;
    }
}

function buscarFacturaEliminar(){
    $result['talonario_busqueda'] = substr(trim(_post('talonario_busqueda')," "),-4);
    $result['factura_busqueda'] = trim(_post('factura_busqueda')," ");
    $result['tipos'] =trim( _post("tipos")," ");
    $result['prefijo'] =strtoupper(trim( _post("prefijo")," "));

    $resultado = getFactura($result['tipos'], $result['prefijo'], $result['talonario_busqueda'],$result['factura_busqueda']);

    return $resultado;
}

function eliminarFactura(){
    $result['id_comprobante'] = _post('id_comprobante');
    $result['talonario_busqueda'] = substr(trim(_post('talonario_busqueda')," "),-4);
    $result['factura_busqueda'] = trim(_post('factura_busqueda')," ");
    $result['tipos'] =trim( _post("tipos")," ");
    $result['prefijo'] =strtoupper(trim( _post("prefijo")," "));


    $sql_ucc = "SELECT id FROM usuarios_cc WHERE id_comprobante = '$result[id_comprobante]'";
    $query_ucc = mysql_query($sql_ucc);

    if($usuarios_cc = mysql_fetch_assoc($query_ucc)){
        $id_usuarioscc = $usuarios_cc['id'];
    }

    if(!$id_usuarioscc){
        $error = "No se pudo eliminar dado que el comprobante no posee una factura asociada en la cuenta corriente";

        $resultado = array('result' => false,'error' => $error);
        return $resultado;
    }else{

        if($result['tipos'] == 'N/C' || $result['tipos'] == 'N/CE'){
            $sql_imputacion = "SELECT ei.id FROM usuarios_cc ucc
                            INNER JOIN erp_imputacion ei on ei.id_cc_cred = ucc.id
                            WHERE ucc.id_comprobante = '$result[id_comprobante]'";
        }else
            $sql_imputacion = "SELECT ei.id FROM usuarios_cc ucc
                            INNER JOIN erp_imputacion ei on ei.id_cc_deb = ucc.id
                            WHERE ucc.id_comprobante = '$result[id_comprobante]'";

        $query_imputacion = mysql_query($sql_imputacion);


        if($imputado = mysql_fetch_assoc($query_imputacion)){
            $id_imputado = $imputado['id'];
        }


        if($id_imputado){
            $error = "No se pudo eliminar dado que el comprobante posee un pago imputado";

            $resultado = array('result' => false,'error' => $error);
            return $resultado;

        }else{

            $sql_asiento = "SELECT id_contab_asientos FROM erp_contab_asientos_vinculos WHERE id_erp_comprobante = '$result[id_comprobante]'";
            $query_asiento = mysql_query($sql_asiento);

            if($asiento_vinculo = mysql_fetch_assoc($query_asiento)){
                $id_asiento = $asiento_vinculo['id_contab_asientos'];
            }

            $sql_contab_asiento = "SELECT numero,concepto FROM erp_contab_asientos WHERE id = '$id_asiento'";
            $asiento = mysql_query($sql_contab_asiento);

            if($resultado_asiento = mysql_fetch_assoc($asiento)){
                $concepto_asiento = $resultado_asiento['concepto'];
                $numero_asiento = $resultado_asiento['numero'];
            }



            if($id_asiento){
                $error = "No se pudo eliminar el comprobante dado que generó el asiento " .$numero_asiento ."-" .$concepto_asiento;

                $resultado = array('result' => false,'error' => $error);
                return $resultado;
            }else{

                $sql_comprobante = "DELETE FROM erp_comprobantes WHERE id = '$result[id_comprobante]'";
                $sql_detalles = "DELETE FROM erp_comprobantes_detalles WHERE id_comprobante = '$result[id_comprobante]'";
                $sql_usuarios_cc = "DELETE FROM usuarios_cc WHERE id_comprobante = '$result[id_comprobante]'";

                loggear("Se eliminó factura ".$result['talonario_busqueda'] ."-".$result['factura_busqueda']."con id_comprobante ".$result['id_comprobante'],"erp_facturacion_tools");

                mysql_query($sql_comprobante);
                $deleted_comprobante = mysql_affected_rows();
                mysql_query($sql_detalles);
                $deleted_detalles = mysql_affected_rows();
                mysql_query($sql_usuarios_cc);
                $deleted_cc = mysql_affected_rows();



                if($deleted_comprobante === 1 AND $deleted_cc === 1 AND $deleted_detalles > 0){
                    $resultado = array('result' => true,'error' => 'ok');
                    return $resultado;
                }else{
                    if($deleted_detalles === 0){
                        $resultado = array('result' => false,'error' => 'No se pudo eliminar. No se encontró detalle para la factura ingresada.','variable' => $result['id_comprobante'],'error2' => $deleted_comprobante,'error3' => $deleted_detalles);
                        return $resultado;
                    }
                    $resultado = array('result' => false,'error' => 'No se pudo eliminar','variable' => $result['id_comprobante'],'error2' => $deleted_comprobante,'error3' => $deleted_detalles);
                    return $resultado;
                }

            }
        }
    }

}

