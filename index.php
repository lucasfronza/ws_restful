<?php

require 'vendor/autoload.php';
require 'models/key_model.php';
require 'models/quiz_model.php';
require 'models/score_board_model.php';
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

// API group
$app->group('/api', function () use ($app, $db) {

    // Version group
    $app->group('/v1', function () use ($app, $db) {

        // Serviço de Quiz - Início
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
                    echo json_encode(array('status' => 0, 'message' => 'Could not save the quiz.'));
                }
            }
            else
            {
                $app->response()->status(500); // 500 = Internal Server Error
                echo json_encode(array('status' => 0, 'message' => 'Could not save the quiz.'));
            }
        });

        # Passando questao, alternativas e resposta, atualiza o Quiz
        $app->put('/quiz/:key', function ($key) use ($app, $db) {
            $key_model = new Key_model($db);
            $quiz_model = new Quiz_model($db);

            $data['key'] = $key;
            $data['question'] = $app->request()->put('question');
            $data['comment'] = $app->request()->put('comment');
            $data['alternative1'] = $app->request()->put('alternative1');
            $data['alternative2'] = $app->request()->put('alternative2');
            $data['alternative3'] = $app->request()->put('alternative3');
            $data['alternative4'] = $app->request()->put('alternative4');
            $data['alternative5'] = $app->request()->put('alternative5');
            $data['correctAnswer'] = $app->request()->put('correctAnswer');

            if (!$key_model->_key_exists($data['key']))
            {
                $app->response()->status(400);
                echo json_encode(array('status' => 0, 'message' => 'Invalid API Key.'));
            } else {

                if ($quiz_model->update($data))
                {
                    $app->response()->status(200);
                    echo json_encode(array('status' => 1, 'message' => 'Quiz updated.'));
                } else {
                    $app->response()->status(500);
                    echo json_encode(array('status' => 0, 'message' => 'Could not save the quiz.')); // 500 = Internal Server Error
                }
            }
        });

        # Retorna o Quiz com pergunta, alternativas e respostas
        $app->get('/quiz/:key', function ($key) use ($app, $db) {
            $key_model = new Key_model($db);
            $quiz_model = new Quiz_model($db);

            if ( ! $key_model->_key_exists($key) )
            {
                $app->response()->status(400);
                echo json_encode(array('status' => 0, 'message' => 'Invalid API Key.'));
            } else {
                $app->response()->status(200);
                echo json_encode($quiz_model->get($key));
            }
        });

        # Deleta um Quiz
        $app->delete('/quiz/:key', function ($key) use ($app, $db) {
            $key_model = new Key_model($db);
            $quiz_model = new Quiz_model($db);

            if ( ! $key_model->_key_exists($key))
            {
                $app->response()->status(400);
                echo json_encode(array('status' => 0, 'message' => 'Invalid API Key.'));
            } else {
                if($quiz_model->delete($key) && $key_model->_delete_key($key))
                {
                    $app->response()->status(200);
                    echo json_encode(array('status' => 1, 'message' => 'Quiz deleted'));
                } else {
                    $app->response()->status(500);
                    echo json_encode(array('status' => 0, 'message' => 'Internal Server Error'));
                }
            }
        });
        // Serviço de Quiz - Fim

        // Serviço de Presença - Início
        // Serviço de Presença - Fim

        // Serviço de Notas - Início
        # Cria um novo Quadro de Notas, retornando um key
        $app->post('/score_board/', function () use ($app, $db) {
            $key_model = new Key_model($db);
            $score_model = new Score_board_model($db);
            // Build a new key
            $key = $key_model->_generate_key();

            // Insert the new key
            if ($key_model->_insert_key($key))
            {
                $app->response()->status(201); // 201 = Created
                echo json_encode(array('status' => 1, 'message' => 'Score board created.', 'key' => $key));
            }
            else
            {
                $app->response()->status(500); // 500 = Internal Server Error
                echo json_encode(array('status' => 0, 'message' => 'Could not generate the key.'));
            }
        });

        # Passando um JSON com uma lista de notas com os IDs dos usuarios e um titulo para a nota, insere os dados no Quadro de Notas
        $app->post('/score_board/:key', function ($key) use ($app, $db) {
            $key_model = new Key_model($db);
            $score_model = new Score_board_model($db);

            $string_json = $app->request()->post('data');

            if (!$key_model->_key_exists($key))
            {
                $app->response()->status(400);
                echo json_encode(array('status' => 0, 'message' => 'Invalid API Key.'));
            } else {
                $json = json_decode($string_json);
                if ($json == NULL) {
                    $app->response()->status(400);
                    echo json_encode(array('status' => 0, 'message' => 'You need to provide a valid JSON named "data".'));
                } else {
                    if (!isset($json->title) || !isset($json->scores)) {
                        $app->response()->status(400);
                        echo json_encode(array('status' => 0, 
                            'message' => 'Your JSON need to have a "title" element and an array named "scores" with "user_id" and "score" elements.
                            Example:
                            {"title":"Test 1", "scores":
                                [
                                    {"user_id":"identfier", "score":10},
                                    {"user_id":3, "score":9.5},
                                    {"user_id":12, "score":9.7}
                                ]
                            }'
                        ));
                    } else {
                        $data['key'] = $key;
                        $data['title'] = $json->title;
                        $activity_id = $score_model->insertActivity($data);

                        foreach ($json->scores as $item) {
                            $data = array();
                            $data['activity_id'] = $activity_id;
                            $data['key'] = $key;
                            $data['user_id'] = $item->user_id;
                            $data['score'] = $item->score;
                            $score_model->insertScore($data);
                        }

                        $app->response()->status(200);
                        echo json_encode(array('status' => 1, 'message' => 'Score board updated.'));
                    }
                }
            }
        });

        # Retorna o Quadro completo
        $app->get('/score_board/:key', function ($key) use ($app, $db) {
            $key_model = new Key_model($db);
            $score_board_model = new Score_board_model($db);

            if ( ! $key_model->_key_exists($key) )
            {
                $app->response()->status(400);
                echo json_encode(array('status' => 0, 'message' => 'Invalid API Key.'));
            } else {
                $app->response()->status(200);
                echo json_encode($score_board_model->get($key));
            }
        });

        # Deleta o Quadro completo
        $app->delete('/score_board/:key', function ($key) use ($app, $db) {
            $key_model = new Key_model($db);
            $score_board_model = new Score_board_model($db);

            if ( ! $key_model->_key_exists($key) )
            {
                $app->response()->status(400);
                echo json_encode(array('status' => 0, 'message' => 'Invalid API Key.'));
            } else {
                if($score_board_model->delete($key) && $key_model->_delete_key($key))
                {
                    $app->response()->status(200);
                    echo json_encode(array('status' => 1, 'message' => 'Score board deleted'));
                } else {
                    $app->response()->status(500);
                    echo json_encode(array('status' => 0, 'message' => 'Internal Server Error'));
                }
            }
        });

        // Serviço de Notas - Fim

    });

});


$app->run();

?>