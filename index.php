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

$permisos = get_permisos($usuario_id, 'erp_facturacion_tools');
if (!in_array('ACCESO', $permisos)) die ("No autorizado");


?>

<!DOCTYPE html>

<html>
<head>
<title>QuadMinds</title>
    <link rel="icon" href="../img/icon_quadminds.ico" type="image/x-icon"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="stylesheet" type="text/css" href="../css/base3.css?v=2"/>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="../css/bootstrap-select.min.css" >
    <link rel="stylesheet" type="text/css" href="../css/bootstrap-multiselect.css" />
    <link rel="stylesheet" type="text/css" href="../css/datepicker3.css" />
    <script type="text/javascript" src="../js/jquery.min.js"></script>
    <script type="text/javascript" src="../js/jquery-ui.min.js"></script>
    <script type="text/javascript" language="javascript" src="../js/bootstrap/bootstrap.min.js"></script>
    <script type="text/javascript" src="../js/functions.js" ></script>
    <script type="text/javascript" src="../js/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap-select-extended.js"></script>
    <script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
    <script src="../js/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="../js/bootstrap-datepicker.es.js" type="text/javascript"></script>


<style type="text/css">

    /* General */

    #main-container{
        display: flex;
        flex-flow: column;

    }

    #title{
        height: 45px;
        background: whitesmoke;
        border-bottom: 1px solid #ccc;
        padding: 0 10px;
    }

    #title > span{
        font-size: 24px;
        text-transform: uppercase;
        vertical-align: middle;
        line-height: 45px;
    }

    #content{
        flex: 1;
        background: white;
        padding: 20px 25px;
        width: 1320px;
        height: auto;
        margin: 0 auto;
        border-left: 1px solid #ccc;
        border-right: 1px solid #ccc;
    }

    .inputDatos {
        width: 300px;
    }
    .multiselect  {
        width: 300px;
    }

    .divDetalle{
        width: 20px;
        margin-left: 10px;
    }

    .inputDetalle{
        margin-bottom: 20px;
        margin-top: 20px;
    }


    .removeBtn{
        margin-bottom: 20px;
    }

</style>
</head>

<body class="home">


<div id="main-container">

    <div id="title">
        <span class="glyphicon glyphicon-compressed" style="margin-right: 10px; font-size: 22px;"></span>
        <span>Tools de Facturacion</span>
    </div>

    <div id="content">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab_a" data-toggle="pill">Alta Comprobante</a></li>
            <li><a href="#tab_b" data-toggle="pill">Alta Cuenta Contable</a></li>
            <li><a href="#tab_c" data-toggle="pill">Eliminar Comprobante</a></li>

        </ul>
        <div class="tab-content col-md-10">
            <div class="tab-pane active" id="tab_a">
                <h3>Alta de Comprobante</h3>

                <p>

                <hr>
                <div class="campo container">
                    <h5><?= _("Usuario"); ?></h5>
                    <div id="divUsuario" class="control-group input-prepend col-sm-3" style="padding-left:0px; padding-right:0px;">
                    </div>
                </div>

                <div class="campo container">
                    <h5><?= _("Tipo Comprobante"); ?></h5>
                    <div id="divTipos" class="control-group input-prepend col-sm-3" style="padding-left:0px; padding-right:0px;">
                    </div>
                </div>


                <div class="campo container">
                    <h5><?= _("Prefijo factura"); ?></h5>
                    <div class="control-group col-sm-3 input-prepend" data-tipo="codigo" style="padding-left:0px; padding-right:0px; ">
                        <span class="add-on"><i class="icon-user"></i></span>
                        <input id="prefijo" type="text" class="form-control inputDatos" maxlength="1" placeholder="Letra Factura">
                        <span class="help-inline"></span>
                    </div>
                </div>

                <div class="campo container">
                    <h5><?= _("Talonario factura"); ?></h5>
                    <div class="control-group col-sm-3 input-prepend" data-tipo="codigo" style="padding-left:0px; padding-right:0px; ">
                        <span class="add-on"><i class="icon-user"></i></span>
                        <input id="talonario" type="text" class="form-control inputDatos" maxlength="5" placeholder="Valor con 5 digitos">
                        <span class="help-inline"></span>
                    </div>
                </div>

                <div class="campo container">
                    <h5><?= _("Numero Factura"); ?></h5>
                    <div class="control-group col-sm-3 input-prepend" data-tipo="codigo" style="padding-left:0px; padding-right:0px; ">
                        <span class="add-on"><i class="icon-user"></i></span>
                        <input id="numeroFactura" type="text" class="form-control inputDatos" maxlength="8" placeholder="Valor 8 digitos">
                        <span class="help-inline"></span>
                    </div>
                </div>

                <div class="campo container">
                    <h5><?= _("Cae"); ?></h5>
                    <div class="control-group col-sm-3 input-prepend" data-tipo="codigo" style="padding-left:0px; padding-right:0px; ">
                        <span class="add-on"><i class="icon-user"></i></span>
                        <input id="cae" type="text" class="form-control inputDatos" placeholder="Valor">
                        <span class="help-inline"></span>
                    </div>
                </div>

                <div class="campo container">
                    <h5><?= _("Vencimiento Cae"); ?></h5>
                    <div class="control-group col-sm-3 input-prepend" data-tipo="codigo" style="padding-left:0px; padding-right:0px; ">
                        <div class='input-group date' id='datepicker3'>
                            <input type='text' id='datepicker3' class="form-control"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <span class="help-inline"></span>
                    </div>
                </div>

                <div class="campo container">
                    <h5><?= _("Fecha emision"); ?></h5>
                    <div class="control-group col-sm-3 input-prepend" data-tipo="codigo" style="padding-left:0px; padding-right:0px; ">
                        <div class='input-group date' id='datepicker'>
                            <input type='text' class="form-control"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <span class="help-inline"></span>
                    </div>
                </div>

                <div class="campo container">
                    <h5><?= _("Fecha vencimiento"); ?></h5>
                    <div class="control-group col-sm-3 input-prepend" data-tipo="codigo" style="padding-left:0px; padding-right:0px; ">
                        <div class='input-group date' id='datepicker2'>
                            <input type='text' class="form-control"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <span class="help-inline"></span>
                    </div>
                </div>



                <div class="campo container">
                    <h5><?= _("Fecha desde"); ?></h5>
                    <div class="control-group col-sm-3 input-prepend" data-tipo="codigo" style="padding-left:0px; padding-right:0px; ">
                        <div class='input-group date' id='datepicker4'>
                            <input type='text' class="form-control"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <span class="help-inline"></span>
                    </div>
                </div>

                <div class="campo container">
                    <h5><?= _("Fecha hasta"); ?></h5>
                    <div class="control-group col-sm-3 input-prepend" data-tipo="codigo" style="padding-left:0px; padding-right:0px; ">
                        <div class='input-group date' id='datepicker5'>
                            <input type='text' class="form-control"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <span class="help-inline"></span>
                    </div>
                </div>

                <h4 style="margin-top: 50px;">Alta Detalle de Facturas</h4>
                <p>

                <hr>

                <button id="add" class="btn btn-success">Agregar Detalle</button>
                <div id="container" class="campo container divDetalle">
                </div>


                <div id="noti_succ" class="alert alert-success" role="alert"
                     style="display: none; margin-top: 30px;margin-bottom: 0;"></div>
                <p id="noti_error" class="alert alert-danger" role="alert"
                     style="display: none; margin-top: 30px;margin-bottom: 0;"></p>

                <br>
                <hr>

                <div style="text-align: left">
                    <button id="btnIngresarFactura" type="button" class="btn btn-primary"><?= _('Ingresar'); ?></button>
                </div>
                </p></div>
            <div class="tab-pane" id="tab_b">
                <p><h3>Alta de Cuenta Contable</h3>
                <p>

                <hr>

                <div class="campo container">
                    <h5><?= _("Numero Cuenta Contable"); ?></h5>
                    <div class="control-group col-sm-3 input-prepend" data-tipo="codigo" style="padding-left:0px; padding-right:0px; ">
                        <span class="add-on"><i class="icon-user"></i></span>
                            <input id="cuentaContable" type="number" class="form-control inputDatos"  onkeypress="if(this.value.length==10) return false;" placeholder="Valor numerico">
                        <span class="help-inline"></span>
                    </div>
                </div>

                <div class="campo container">
                    <h5><?= _("Descripcion Cuenta Contable"); ?></h5>
                    <div class="control-group col-sm-3 input-prepend" data-tipo="codigo" style="padding-left:0px; padding-right:0px; ">
                        <span class="add-on"><i class="icon-user"></i></span>
                        <input id="descripcionCuenta" type="text" class="form-control inputDatos" maxlength="255" placeholder="Descripcion Cuenta Contable">
                        <span class="help-inline"></span>
                    </div>
                </div>

                <div class="campo container">
                    <h5><?= _("Imputable"); ?></h5>
                    <div id="imputable" class="control-group input-prepend col-sm-3" style="padding-left:0px; padding-right:0px;">
                        <select id="selectImputable" class="selectImputable form-control inputDatos">
                            <option value="">Seleccionar Si o No</option>
                            <option value="1">Si</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>

                <div class="campo container">
                    <h5><?= _("Moneda"); ?></h5>
                    <div id="imputable" class="control-group input-prepend col-sm-3" style="padding-left:0px; padding-right:0px;">
                        <select id="selectMoneda" class="selectMoneda form-control inputDatos">
                            <option value="">Seleccionar Moneda</option>
                            <option value="ARS">ARS</option>
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                        </select>
                    </div>
                </div>

                <div id="noti_CCsucc" class="alert alert-success" role="alert" style="display: none; margin-top: 30px;margin-bottom: 0;"></div>
                <div id="noti_CCError" class="alert alert-danger" role="alert" style="display: none; margin-top: 30px;margin-bottom: 0;"></div>

                <br>
                <br>
                <hr>
                <div style="text-align: left">
                    <button id="btnIngresarCC" type="button" class="btn btn-primary"><?= _('Ingresar'); ?></button>

                </div>
            </div>



            <div class="tab-pane" id="tab_c">
                <p><h3>Eliminar Comprobante</h3>


                <div class="campo container">
                    <h5><?= _("Tipo Comprobante"); ?></h5>
                    <div id="imputable" class="control-group input-prepend col-sm-3" style="padding-left:0px; padding-right:0px;">
                        <select id="tipo" class="selectImputable form-control inputDatos">
                            <option value="">Seleccionar Tipo de comprobante</option>
                            <option value="FAC">FAC</option>
                            <option value="N/C">N/C</option>
                            <option value="N/CE">N/CE</option>
                            <option value="N/D">N/D</option>
                            <option value="N/DE">N/DE</option>
                        </select>
                    </div>
                </div>


                <div class="campo container">
                    <h5><?= _("Prefijo factura"); ?></h5>
                    <div class="control-group col-sm-3 input-prepend" data-tipo="codigo" style="padding-left:0px; padding-right:0px; ">
                        <span class="add-on"><i class="icon-user"></i></span>
                        <input id="prefijoEliminar" type="text" class="form-control inputDatos" maxlength="1" placeholder="Letra Factura">
                        <span class="help-inline"></span>
                    </div>
                </div>

                <div class="campo container">
                    <h5><?= _("Numero de Talonario"); ?></h5>
                    <div class="control-group col-sm-3 input-prepend" data-tipo="codigo" style="padding-left:0px; padding-right:0px; ">
                        <span class="add-on"><i class="icon-user"></i></span>
                        <input id="talonarioBusqueda" type="text" class="form-control inputDatos" maxlength="5" placeholder="Valor">
                        <span class="help-inline"></span>
                    </div>
                </div>

                <div class="campo container">
                    <h5><?= _("Numero de Comprobante"); ?></h5>
                    <div class="control-group col-sm-3 input-prepend" data-tipo="codigo" style="padding-left:0px; padding-right:0px; ">
                        <span class="add-on"><i class="icon-user"></i></span>
                        <input id="numeroFacturaBusqueda" type="text" class="form-control inputDatos" maxlength="8" placeholder="Valor">
                        <span class="help-inline"></span>
                    </div>
                </div>


                <div class="campo container" id="datosFactura" style="margin-top: 50px">
                    <h4>Los datos de la factura son:</h4>
                    <p id="datosTipoFactura"></p>
                    <p id="datosPrefijo"></p>
                    <p id="datosFacturaTalonario"></p>
                    <p id="datosFacturaNumero"></p>
                    <p id="datosFacturaUsuario"></p>
                    <p id="datosFacturaFecha"></p>
                    <input id="idComprobante" type="text" class="form-control inputDatos" style="visibility: hidden;">

                    <div style="text-align: left">
                        <button id="btnEliminarFactura" type="button" class="btn btn-primary"><?= _('Eliminar Factura'); ?></button>
                    </div>
                </div>


                <div id="noti_facBorradosucc" class="alert alert-success" role="alert" style="display: none; margin-top: 30px;margin-bottom: 0;"></div>
                <div id="noti_facBorradoError" class="alert alert-danger" role="alert" style="display: none; margin-top: 30px;margin-bottom: 0;"></div>

                <br>
                <br>
                <div style="text-align: left">
                    <button id="btnBuscarFactura" type="button" class="btn btn-primary"><?= _('Buscar'); ?></button>

                </div>
            </div>
        </div><!-- tab content -->
    </div>
</div>
<script src="erp_facturacion_tools.js" type="text/javascript"></script>

</body>
</html>
