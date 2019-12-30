<?php

require('Db.php');

$created_on = $last_modified_on = date('Y-m-d H:i:s');
$fech_ingreso = time();

$asociado['apellido'] = 'Doe';
$asociado['nombre'] = 'John';
$asociado['genero'] = 'Masculino';
$asociado['nro_cuil'] = '20754789872';
$asociado['tipo_documento'] = 'DNI';
$asociado['nro_documento'] = '12345678';
$asociado['categoria'] = 'ACTIVO';
$asociado['fech_nacimiento'] = '2000-12-29';
$asociado['fech_ingreso'] = $fech_ingreso;
$asociado['tel_movil'] = '2616514785';
$asociado['tel_linea'] = '2614200192';
$asociado['email'] = 'testing@gmail.com';
$asociado['domicilio'] = 'Alem 184';
$asociado['localidad'] = '3478';
$asociado['created_on'] = $created_on;
$asociado['last_modified_on'] = $last_modified_on;

$message = '';

if( eliminarAsociado(2) )
{
    $message = 'El registro fue eliminado con éxito.';
} else{
    $message = 'El registro no existe o ya fue eliminado.';
}

/**
 * https://www.sqlite.org/foreignkeys.html
 * sqlite> PRAGMA foreign_keys; 1 o 0 para ON o OFF respectivamente
 * sqlite> PRAGMA foreign_keys = ON;
 */

// if( insertarAsociado($asociado) )
// {
//     $message = 'El registro fue insertado con éxito.';
// }

echo $message;

function insertarAsociado( $asociado )
{
    $q = 'INSERT INTO asociado (
            apellido,
            nombre,
            genero,
            nro_cuil,
            tipo_documento,
            nro_documento,
            categoria,
            fech_nacimiento,
            fech_ingreso,
            domicilio,
            id_localidad,
            created_on,
            last_modified_on
            ) 
          VALUES (:apellido, :nombre, :genero, :nro_cuil, :tipo_documento, :nro_documento, :categoria, :fech_nacimiento, :fech_ingreso, :domicilio, :id_localidad, :created_on, :last_modified_on);';

    $stmt = Db::getInstance()->prepare($q);

    $data = [
        ':apellido' => $asociado['apellido'],
        ':nombre' => $asociado['nombre'],
        ':genero' => $asociado['genero'],
        ':nro_cuil' => $asociado['nro_cuil'],
        ':tipo_documento' => $asociado['tipo_documento'],
        ':nro_documento' => $asociado['nro_documento'],
        ':categoria' => $asociado['categoria'],
        ':fech_nacimiento' => $asociado['fech_nacimiento'],
        ':fech_ingreso' => $asociado['fech_ingreso'],
        ':domicilio' => $asociado['domicilio'],
        ':id_localidad' => $asociado['localidad'],
        ':created_on' => $asociado['created_on'],
        ':last_modified_on' => $asociado['last_modified_on']
    ];

    $stmt->execute($data);

    if($stmt->rowCount() != 1) {
        return false;
    }

    // Grabamos el ID del asociado
    $id_asociado = Db::getInstance()->lastInsertId();

    $q = 'INSERT INTO email (email, id_asociado) VALUES (:email, :id_asociado);';
    
    $stmt = Db::getInstance()->prepare($q);
    
    $data = [
        ':email' => $asociado['email'],
        ':id_asociado' => $id_asociado
    ];
    
    $stmt->execute($data);
    
    if($stmt->rowCount() != 1){
        return false;
    }

    if( !insertarTelefono($id_asociado, $asociado['tel_movil']) ){
        return false;
    }

    if(!empty($asociado['tel_linea'])) {
        if( !insertarTelefono($id_asociado, $asociado['tel_linea'], 'linea') ){
            return false;
        }
    }
    return true;
}

function insertarTelefono($id_asociado, $tel, $tipo = 'movil')
{
    $q = 'INSERT INTO telefono (telefono, tipo, id_asociado) VALUES (:telefono, :tipo, :id_asociado);';
    
    $stmt = Db::getInstance()->prepare($q);
    
    $data = [
        ':telefono' => $tel,
        ':tipo' => $tipo,
        ':id_asociado' => $id_asociado
    ];
    
    $stmt->execute($data);
    
    return ($stmt->rowCount() == 1);
}

function eliminarAsociado($id_asociado)
{
    $q = 'DELETE FROM asociado
          WHERE id_asociado = :id_asociado;';

    $stmt = Db::getInstance()->prepare($q);

    $data = [
        ':id_asociado' => $id_asociado
    ];

    $stmt->execute($data);

    return ($stmt->rowCount() == 1);
}