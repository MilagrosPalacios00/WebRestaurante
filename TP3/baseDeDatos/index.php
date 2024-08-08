<?php 
/* TP3 */
require 'vendor/autoload.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

include 'src/Models/Db.php';
$app = AppFactory::create();

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Headers:X-Request-With');

header('Access-Control-Allow-Methods: GET, POST, DELETE,PUT');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');


//nueva api para obtener los datos de un item por id.

$app->get('/api/item/obtenerporid/{id}', function(Request $request, Response $response){
    $id_item = $request->getAttribute('id');

    if (!is_numeric($id_item)) {
        return $response->withHeader("content-type","application/json")->withStatus(400, "id inválido : tipo de dato incorrecto");
    }

    $sql = "SELECT * FROM items_menu WHERE id = :id_item"; 

    try {
        $db = new DB();
        $db = $db->conectDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':id_item', $id_item); 
        $resultado->execute();

        if ($resultado->rowCount() > 0) {
            $item = $resultado->fetch(PDO::FETCH_OBJ);
            $response->getBody()->write(json_encode($item));
            return $response->withHeader("content-type","application/json")->withStatus(200, "OK");
        } else {
            throw new PDOException("No existe item con ese id");
        }
        
        $resultado = null;
        $db = null;
    } catch (PDOException $e) {
        return $response->withHeader("content-type", "application/json")->withStatus(404, json_encode(["error" => ["text" => $e->getMessage()]]));
    }
});

//d)Obtener los ítems ordenados por precio: implemente un endpoint para obtener los ítems de la tabla, 
//permitiendo pasar los filtros por tipo (comida/bebida) y por nombre de producto (parcial o totalmente,
//por ejemplo, “gu” encontraría agua y hamburguesa) 
//así como también si el orden será ascendente o descendente.
//Si no paso los filtros, todos los registros serán devueltos. sss
//Si no paso el orden, por defectoserá ascendente.

$app->get('/api/items/obtener', function (Request $request, Response $response) {
    $queryParams = $request->getQueryParams();
    
    $sql = "SELECT * FROM items_menu WHERE 1=1";

    $params = [];
    $whereClauses = [];

    if (!empty($queryParams['nombre'])) {
        $whereClauses[] = "nombre LIKE :nombre";
        $params[':nombre'] = "%" . $queryParams['nombre'] . "%";
    }

    if (!empty($queryParams['tipo'])) {
        $whereClauses[] = "tipo = :tipo";
        $params[':tipo'] = $queryParams['tipo'];
    }
  
    if (!empty($whereClauses)) {
        $sql .= " AND " . implode(" AND ", $whereClauses);
    }

    if (empty($queryParams['orden'])) {
        $orden = "ASC";
    } else {
        $orden = "DESC";
    }

    $sql .= " ORDER BY precio " . $orden;

    try {
        $db = new DB();
        $db = $db->conectDB();
        $resultado = $db->prepare($sql);

        foreach ($params as $key => &$value) {
            $resultado->bindParam($key, $value);
        }

        $resultado->execute();
        $items = $resultado->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as &$item) {
            if (!empty($item['imagen'])) {
                $item['imagen'] = $item['imagen'];
            }
        }

        $response->getBody()->write(json_encode($items));
        return $response->withHeader("Content-Type", "application/json")->withStatus(200, "OK");

        $resultado = null;
        $db = null;
    } catch (PDOException $e) {
        echo '{"error": {"text": ' . $e->getMessage() . '}}';
    }
});


//a)Crear un nuevo ítem: implementar un endpoint para crear un nuevo ítem en la tabla de items_menu.
//El endpoint debe permitir enviar el nombre, el precio, el tipo (comida o bebida) y la foto.


$app->post('/api/items/nuevo', function(Request $request, Response $response){
    $parsedBody = $request->getParsedBody(); 
 
    
       if (empty($parsedBody['nombre']) || empty($parsedBody['precio']) || empty($parsedBody['tipo'])  || empty($parsedBody['imagen']) || empty($parsedBody['tipo_imagen'])) {
        $error_message = "Faltan datos para crear un nuevo ítem. Los siguientes atributos son obligatorios: ";
        $missing_attributes = [];

        if (empty($parsedBody['nombre'])) {
            $missing_attributes[] = "nombre";
        }
        if (empty($parsedBody['precio'])) {
            $missing_attributes[] = "precio";
        }
        if (empty($parsedBody['tipo'])) {
            $missing_attributes[] = "tipo";
        }
        if (empty($parsedBody['tipo_imagen'])) { 
            $missing_attributes[] = "tipo_imagen";
        }
        if (empty($parsedBody['imagen'])) {
            $missing_attributes[] = "imagen";
        }
        $error_message .= implode(", ", $missing_attributes); 
        return $response->withHeader("content-type","application/json")->withStatus(400, $error_message);
    }

if (!is_string($parsedBody['nombre']) || is_numeric($parsedBody['nombre']) || !is_numeric($parsedBody['precio']) || !is_string($parsedBody['tipo']) || is_numeric($parsedBody['tipo']) || !is_string($parsedBody['imagen']) || is_numeric($parsedBody['imagen'])|| !is_string($parsedBody['tipo_imagen']) || is_numeric($parsedBody['tipo_imagen'])) {
    $error_message = "Error en el tipo de dato en los siguientes atributos: ";
    $missing_dataType = [];

    if (!is_string($parsedBody['nombre']) || is_numeric($parsedBody['nombre'])) {
        $missing_dataType[] = "nombre";
    }

    if (!is_numeric($parsedBody['precio'])) {
        $missing_dataType[] = "precio";
    }

    if (!is_string($parsedBody['tipo']) || is_numeric($parsedBody['tipo'])) {
        $missing_dataType[] = "tipo";
    }

    if (!is_string($parsedBody['imagen']) || is_numeric($parsedBody['imagen'])) {
        $missing_dataType[] = "imagen";
    }

    if (!is_string($parsedBody['tipo_imagen']) || is_numeric($parsedBody['tipo_imagen'])) {
        $missing_dataType[] = "tipo_imagen";
    }

    $error_message .= implode(", ", $missing_dataType); 
    return $response->withHeader("content-type","application/json")->withStatus(400, $error_message);

}

    $sql = "INSERT INTO items_menu (nombre, precio, tipo, imagen, tipo_imagen) VALUES (:nombre, :precio, :tipo, :imagen, :tipo_imagen)";
   
    try {
        $db = new DB();
        $db = $db->conectDB();
        $resultado = $db->prepare($sql);

        $resultado->bindParam(':nombre', $parsedBody['nombre']);
        $resultado->bindParam(':precio', $parsedBody['precio']);
        $resultado->bindParam(':tipo', $parsedBody['tipo']);
        $resultado->bindParam(':imagen', $parsedBody['imagen']);
        $resultado->bindParam(':tipo_imagen', $parsedBody['tipo_imagen']);


        $resultado->execute();
        return $response->withHeader("content-type","application/json")->withStatus(200, "OK");

        $resultado = null;
        $db = null;
    } catch (PDOException $e) {
        echo '{"error": {"text": ' . $e->getMessage() . '}}';
    }
});

//b)Actualizar información de un ítem: implementar un endpoint para actualizar la información 
//de un ítem existente en la tabla items_menu. El endpoint debe permitir 
//enviar el id y los campos que se quieran actualizar.
$app->put('/api/items/modificar/{id}', function(Request $request, Response $response){
    $id_item = $request->getAttribute('id'); 
    $data = $request->getBody()->getContents();
    $parsedBody = json_decode($data, true);

    try {
        $existeitem = "SELECT id FROM items_menu WHERE id = :id_item";
        $db = new DB();
        $db = $db->conectDB();
        $stmt = $db->prepare($existeitem); 
        $stmt->bindParam(':id_item', $id_item);
        $stmt->execute();

        $resultado = $stmt->fetch();

        if (!$resultado) {
            throw new PDOException("No existe item con ese id");
        }

    if (!empty($parsedBody['nombre']) && is_numeric($parsedBody['nombre']) || !empty($parsedBody['precio']) && !is_numeric($parsedBody['precio']) 
        || !empty($parsedBody['tipo']) && is_numeric($parsedBody['tipo']) || !empty($parsedBody['imagen']) &&  is_numeric($parsedBody['imagen'])|| !empty($parsedBody['tipo_imagen']) && is_numeric($parsedBody['tipo_imagen'])){
        $errores = [];
        $errores[] = "Error en el tipo de dato : ";
        
        if (!empty($parsedBody['nombre'])) {
            if (is_numeric($parsedBody['nombre'])) {
                $errores[] = " nombre ";
            }
        }

        if (!empty($parsedBody['precio'])) {
            if (!is_numeric($parsedBody['precio'])) {
                $errores[] = " precio ";
            }
        }

        if (!empty($parsedBody['tipo'])) {
            if (is_numeric($parsedBody['tipo'])) {
                $errores[] = " tipo ";
            }
        }

        if (!empty($parsedBody['imagen']) && !is_numeric($parsedBody['imagen'])) {
            $errores[] = " imagen ";
        }
        
        if (!empty($parsedBody['tipo_imagen'])) {
            if (is_numeric($parsedBody['tipo_imagen'])) {
                $errores[] = " tipo_imagen ";
            }
        }
        
        if (!empty($errores)) {
            $mensajeError = implode(", ", $errores);
            return $response->withHeader("content-type", "application/json")->withStatus(400, $mensajeError);
        }
    }
       
            
        $sql = "UPDATE items_menu SET ";
        $resultado = [];

        if (isset($parsedBody['nombre'])) {
            $resultado[] = "nombre = :nombre";
        }
        if (isset($parsedBody['precio'])) {
            $resultado[] = "precio = :precio";
        }
        if (isset($parsedBody['tipo'])) {
            $resultado[] = "tipo = :tipo";
        }
        if (isset($parsedBody['imagen'])) {
            $resultado[] = "imagen = :imagen";
        }
        if (isset($parsedBody['tipo_imagen'])) {
            $resultado[] = "tipo_imagen = :tipo_imagen";
        }
        if (count($resultado) === 0) {
            return $response->withHeader("content-type", "application/json")->withStatus(400, "No hay datos para actualizar");
        }

        $sql .= implode(', ', $resultado) . " WHERE id = :id_item";

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':id_item', $id_item);

        if (isset($parsedBody['nombre'])) {
            $stmt->bindParam(':nombre', $parsedBody['nombre']);
        }
        if (isset($parsedBody['precio'])) {
            $stmt->bindParam(':precio', $parsedBody['precio']);
        }
        if (isset($parsedBody['tipo'])) {
            $stmt->bindParam(':tipo', $parsedBody['tipo']);
        }
        if (isset($parsedBody['imagen'])) {
            $stmt->bindParam(':imagen', $parsedBody['imagen']);
        }
        if (isset($parsedBody['tipo_imagen'])) {
            $stmt->bindParam(':tipo_imagen', $parsedBody['tipo_imagen']);
        }

        $stmt->execute();
        return $response->withHeader("content-type", "application/json")->withStatus(200, "OK");

        $stmt = null;
        $db = null;
    } catch (PDOException $e) {
        return $response->withHeader("content-type", "application/json")->withStatus(404, json_encode(["error" => ["text" => $e->getMessage()]]));
    }
});

//e)Obtener todos los pedidos del más nuevo al más viejo
$app->get('/api/pedidos/obtener', function(Request $request, Response $response){
   
    $sql = "SELECT P.id AS pedido_id, P.idItemMenu, P.nromesa, P.comentarios, P.fechaAlta, 
                   IT.id AS item_id, IT.nombre, IT.precio, IT.tipo, IT.imagen 
            FROM `pedidos` P 
            INNER JOIN `items_menu` IT ON (P.idItemMenu = IT.id) 
            ORDER BY P.fechaAlta DESC";

    try {
        $db = new DB();
        $db = $db->conectDB(); 
        $resultado = $db->query($sql);

        if ($resultado->rowCount() > 0) {
            $pedidos = $resultado->fetchAll(PDO::FETCH_OBJ);
            $response->getBody()->write(json_encode($pedidos));
            return $response->withHeader("content-type", "application/json")->withStatus(200, "OK");
        } else {
            return $response->withHeader("content-type", "application/json")->withStatus(404, "No existen pedidos en la base de datos");
        }

        $resultado = null;
        $db = null;
    } catch (PDOException $e) {
        echo '{"error" : {"text":' . $e->getMessage() . '}';
    }
});


//f)Crear un nuevo pedido: implementar un endpoint para crear un nuevo pedido en la tabla de pedidos. 
//El endpoint debe permitir enviar el número de mesa, el id del ítem del menú y un comentario (opcionalmente).
$app->post('/api/pedidos/nuevo', function(Request $request, Response $response){

    $parsedBody = $request->getParsedBody(); 

    if (empty($parsedBody['nro_mesa']) || empty($parsedBody['id_item_menu'])) {
        $error_message = "El siguiente campo está incompleto:";
        $missing_dataType = [];
    
        if (empty($parsedBody['nro_mesa'])) {
            $missing_dataType[] = "nro_mesa";
        }
    
        if (empty($parsedBody['id_item_menu'])) {
            $missing_dataType[] = "id_item_menu";
        }
    
        $error_message .= implode(", ", $missing_dataType);
        return $response->withHeader("content-type", "application/json")->withStatus(400, $error_message);
    }
    

    if (!is_numeric($parsedBody['nro_mesa'])) {
        return $response->withHeader("content-type","application/json")->withStatus(400, "El tipo de dato ingesado en mesa es invalido");
    }

    if (!is_numeric($parsedBody['id_item_menu'])) {
        return $response->withHeader("content-type","application/json")->withStatus(400, "El tipo de dato ingesado en id item menu es invalido");
    }

    $sql = "INSERT INTO pedidos (nromesa, idItemMenu, comentarios, fechaAlta) VALUES (:nro_mesa, :id_item_menu, :comentarios, NOW())";

    try {
        $db = new DB();
        $db = $db->conectDB();
        $resultado = $db->prepare($sql);

        $resultado->bindParam(':nro_mesa', $parsedBody['nro_mesa']);
        $resultado->bindParam(':id_item_menu', $parsedBody['id_item_menu']);
        $resultado->bindParam(':comentarios', $parsedBody['comentarios']);

        $resultado->execute();
        return $response->withHeader("content-type","application/json")->withStatus(200, "OK");

        $resultado = null;
        $db = null;
    } catch (PDOException $e) { 
        return $response->withHeader("content-type", "application/json")->withStatus(400, json_encode(["error" => ["text" => "el id del menu no existe en la base de datos" ,$e->getMessage()]]));
    }
});

//h)Eliminar un pedido: el endpoint debe borrar un pedido a partir de su id.
$app->delete('/api/pedidos/delete/{id}', function(Request $request, Response $response){
    $id_pedido = $request->getAttribute('id');

    /*if (!is_numeric($id_pedido)) {
        return $response->withHeader("content-type","application/json")->withStatus(400, "id inválido: tipo de dato incorrecto");      
    }*/

    $sql = "DELETE FROM pedidos WHERE id = :id_pedido";

    try {
        $db = new DB();
        $db = $db->conectDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':id_pedido', $id_pedido);
        $resultado->execute();

        if ($resultado->rowCount() > 0) {
            return $response->withHeader("content-type","application/json")->withStatus(200, "OK");
        } else {
            throw new PDOException("No existe pedido con ese id");
        }

        $resultado = null;
        $db = null;
    } catch (PDOException $e) {
        return $response->withHeader("content-type", "application/json")->withStatus(404, json_encode(["error" => ["text" => $e->getMessage()]]));
    }
});

//g)Obtener un pedido: implementar un endpoint que permita obtener un pedido a partir de
//su id. Este debe mostrar todos los datos del pedido, incluyendo el nombre del item del
//menú, precio, tipo y foto
$app->get('/api/pedidos/obtenerporid/{id}', function(Request $request, Response $response){
    $id_pedido = $request->getAttribute('id');

    
    if (!is_numeric($id_pedido)) {
        return $response->withHeader("content-type","application/json")->withStatus(400, "id inválido : tipo de dato incorrecto");
    }

    $sql = "SELECT p.id, p.nromesa, p.comentarios, p.fechaAlta,
            i.nombre AS nombre_item, i.precio, i.tipo, i.imagen
            FROM pedidos p
            JOIN items_menu i ON p.idItemMenu = i.id
            WHERE p.id = :id_pedido";
       
    try {
        $db = new DB();
        $db = $db->conectDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':id_pedido', $id_pedido);
        $resultado->execute();

        if ($resultado->rowCount() > 0) {
            $pedido = $resultado->fetch(PDO::FETCH_OBJ);
            $response->getBody()->write(json_encode($pedido));
            return $response->withHeader("content-type","application/json")->withStatus(200, "OK");
        } else {
            throw new PDOException("No existe pedido con ese id");
        }
        
        $resultado = null;
        $db = null;
    } catch (PDOException $e) {
        return $response->withHeader("content-type", "application/json")->withStatus(404, json_encode(["error" => ["text" => $e->getMessage()]]));
    }
    });

    //c)Eliminar un ítem: el endpoint debe permitir enviar el id del ítem y eliminarlo de la tabla solo
//si no existen pedidos para ese ítem. 
$app->delete('/api/items/delete/{id}', function(Request $request, Response $response){
    $id= $request->getAttribute('id');
    try{
    $db = new DB();
    $db = $db->conectDB(); 

    // Verificar si existen pedidos relacionados con el cliente
    $verificar_pedidos = "SELECT COUNT(*) as cantidad_pedidos FROM pedidos WHERE idItemMenu = :id";
    $resultado = $db->prepare($verificar_pedidos);
    $resultado->bindParam(':id', $id);
    $resultado->execute();
    $row_pedidos = $resultado->fetch(PDO::FETCH_ASSOC);
    $cantidad_pedidos = $row_pedidos['cantidad_pedidos'];

    if ($cantidad_pedidos > 0) {
        return $response->withHeader("content-type","application/json")->withStatus(409, "No se puede eliminar el item, existen pedidos relacionados");
    } else {
        // Si no hay pedidos relacionados se elimina
        $eliminar = "DELETE FROM items_menu WHERE id = :id";
        $resultado_eliminar = $db->prepare($eliminar);
        $resultado_eliminar->bindParam(':id', $id);
        $resultado_eliminar->execute();

        if ($resultado_eliminar->rowCount() > 0) {
            return $response->withHeader("content-type","application/json")->withStatus(200, "OK");
        } else {
            return $response->withHeader("content-type","application/json")->withStatus(404, "No existe item con ese id");
        }
    }
    $resultado = null;
    $resultado_eliminar = null;
    $db = null;
} catch (PDOException $e) {
    echo '{"error": {"text": ' . $e->getMessage() . '}}';
}

});

 $app->run();