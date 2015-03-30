<?php

require 'vendor/autoload.php';
require 'models/key_model.php';
require 'models/quiz_model.php';
\Slim\Slim::registerAutoloader();

// Configuração do Banco de Dados
$db = new medoo([
    'database_type' => 'mysql',
    'database_name' => 'ic_webservices_bd',
    'server' => 'localhost',
    'username' => 'ic_ws_user',
    'password' => '',
    'charset' => 'utf8'
]);

$app = new \Slim\Slim();
$app->response()->header('Content-Type', 'application/json;charset=utf-8');

/*
$app->get('/', function () {
    echo "SlimProdutos ";
});
*/

// API group
$app->group('/api', function () use ($app, $db) {

    // Version group
    $app->group('/v1', function () use ($app, $db) {

        // Serviço de Quiz
        # Cria um novo Quiz, retornando um key
        $app->post('/quiz/', function () use ($app, $db) {
            $key_model = new Key_model($db);
            $quiz_model = new Quiz_model($db);
            // Build a new key
            $key = $key_model->_generate_key();

            // Insert the new key
            if ($key_model->_insert_key($key))
            {

                $data['key'] = $key;
                $data['question'] = $app->request()->post('question');
                $data['comment'] = $app->request()->post('comment');
                $data['alternative1'] = $app->request()->post('alternative1');
                $data['alternative2'] = $app->request()->post('alternative2');
                $data['alternative3'] = $app->request()->post('alternative3');
                $data['alternative4'] = $app->request()->post('alternative4');
                $data['alternative5'] = $app->request()->post('alternative5');
                $data['correctAnswer'] = $app->request()->post('correctAnswer');

                if ($quiz_model->insert($data))
                {
                    $app->response()->status(201); // 201 = Created
                    echo json_encode(array('status' => 1, 'message' => 'Quiz created.', 'key' => $key));
                } else {
                    $app->response()->status(500); // 500 = Internal Server Error
                    echo json_encode(array('status' => 0, 'error' => 'Could not save the quiz.'));
                }
            }
            else
            {
                $app->response()->status(500); // 500 = Internal Server Error
                echo json_encode(array('status' => 0, 'error' => 'Could not save the quiz.'));
            }
        });


        // Exemplo: contacts
        // GET route
        $app->get('/contacts/:id', function ($id) use ($app, $db) {
            echo "Contato $id";
            $model = new Key_model($db);
            var_dump($model->_key_exists('c14fa1e82b559cc9570e827ff884ba5a5504c4ae'));
            /*$datas = $db->select("keys", "*");*/
            /*echo json_encode($datas, JSON_PRETTY_PRINT);*/
        });

        // PUT route, for updating
        $app->put('/contacts/:id', function ($id) use ($app, $db) {

        });

        // DELETE route
        $app->delete('/contacts/:id', function ($id) {

        });

        $app->post('/contacts/', function () use ($app, $db) {
            echo "teste";
            $body = $app->request()->getBody();
            
            echo $app->request()->post('key');
        });

    });

});


/*$app->get('/categorias','getCategorias');
$app->post('/produtos','addProduto');
$app->get('/produtos/:id','getProduto');
$app->post('/produtos/:id','saveProduto');
$app->delete('/produtos/:id','deleteProduto');
$app->get('/produtos','getProdutos');*/

$app->run();


/*
function getConn()
{
    return new PDO('mysql:host=localhost;dbname=SlimProdutos',
      'root',
      '',
      array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")

      );

}

function getCategorias()
{
  $stmt = getConn()->query("SELECT * FROM Categorias");
  $categorias = $stmt->fetchAll(PDO::FETCH_OBJ);
  echo "{\"categorias\":".json_encode($categorias)."}";
}

function addProduto()
{
  $request = \Slim\Slim::getInstance()->request();
  $produto = json_decode($request->getBody());
  $sql = "INSERT INTO produtos (nome,preco,dataInclusao,idCategoria) values (:nome,:preco,:dataInclusao,:idCategoria) ";
  $conn = getConn();
  $stmt = $conn->prepare($sql);
  $stmt->bindParam("nome",$produto->nome);
  $stmt->bindParam("preco",$produto->preco);
  $stmt->bindParam("dataInclusao",$produto->dataInclusao);
  $stmt->bindParam("idCategoria",$produto->idCategoria);
  $stmt->execute();
  $produto->id = $conn->lastInsertId();
  echo json_encode($produto);
}

function getProduto($id)
{
  $conn = getConn();
  $sql = "SELECT * FROM produtos WHERE id=:id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam("id",$id);
  $stmt->execute();
  $produto = $stmt->fetchObject();

  //categoria
  $sql = "SELECT * FROM categorias WHERE id=:id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam("id",$produto->idCategoria);
  $stmt->execute();
  $produto->categoria =  $stmt->fetchObject();

  echo json_encode($produto);
}

function saveProduto($id)
{
  $request = \Slim\Slim::getInstance()->request();
  $produto = json_decode($request->getBody());
  $sql = "UPDATE produtos SET nome=:nome,preco=:preco,dataInclusao=:dataInclusao,idCategoria=:idCategoria WHERE id=:id";
  $conn = getConn();
  $stmt = $conn->prepare($sql);
  $stmt->bindParam("nome",$produto->nome);
  $stmt->bindParam("preco",$produto->preco);
  $stmt->bindParam("dataInclusao",$produto->dataInclusao);
  $stmt->bindParam("idCategoria",$produto->idCategoria);
  $stmt->bindParam("id",$id);
  $stmt->execute();
  echo json_encode($produto);

}

function deleteProduto($id)
{
  $sql = "DELETE FROM produtos WHERE id=:id";
  $conn = getConn();
  $stmt = $conn->prepare($sql);
  $stmt->bindParam("id",$id);
  $stmt->execute();
  echo "{'message':'Produto apagado'}";
}

function getProdutos()
{
  $sql = "SELECT *,Categorias.nome as nomeCategoria FROM Produtos,Categorias WHERE Categorias.id=Produtos.idCategoria";
  $stmt = getConn()->query($sql);
  $produtos = $stmt->fetchAll(PDO::FETCH_OBJ);
  echo "{\"produtos\":".json_encode($produtos)."}";
}

*/