<?php

require 'vendor/autoload.php';
require 'models/key_model.php';
require 'models/quiz_model.php';
require 'models/score_board_model.php';
require 'models/attendance_board_model.php';
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
        # Cria um novo Quadro de Presença, retornando um key
        $app->post('/attendance_board/', function () use ($app, $db) {
            $key_model = new Key_model($db);
            $attendance_model = new Attendance_board_model($db);
            // Build a new key
            $key = $key_model->_generate_key();

            // Insert the new key
            if ($key_model->_insert_key($key))
            {
                $app->response()->status(201); // 201 = Created
                echo json_encode(array('status' => 1, 'message' => 'Attedance board created.', 'key' => $key));
            }
            else
            {
                $app->response()->status(500); // 500 = Internal Server Error
                echo json_encode(array('status' => 0, 'message' => 'Could not generate the key.'));
            }
        });

        # Passando um JSON com uma lista de presença com os IDs dos usuarios e um titulo para a presença, insere os dados no Quadro de Presença
        $app->post('/attendance_board/:key', function ($key) use ($app, $db) {
            $key_model = new Key_model($db);
            $attendance_model = new Attendance_board_model($db);

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
                    if (!isset($json->title) || !isset($json->attendances)) {
                        $app->response()->status(400);
                        echo json_encode(array('status' => 0, 
                            'message' => 'Your JSON need to have a "title" element and an array named "attendances" with "user_id" and "attended" elements.
                            Example:
                            {"title":"Aula Extra 28-03-2015", "attendances":  
                                [  
                                    {"user_id":8, "attended":1},  
                                    {"user_id":3, "attended":0},  
                                    {"user_id":12, "attended":1}  
                                ]  
                            }'
                        ));
                    } else {
                        $data['key'] = $key;
                        $data['title'] = $json->title;
                        $activity_id = $attendance_model->insertActivity($data);

                        foreach ($json->attendances as $item) {
                            $data = array();
                            $data['activity_id'] = $activity_id;
                            $data['key'] = $key;
                            $data['user_id'] = $item->user_id;
                            $data['attended'] = $item->attended;
                            $attendance_model->insertAttendance($data);
                        }

                        $app->response()->status(200);
                        echo json_encode(array('status' => 1, 'message' => 'Attendance board updated.'));
                    }
                }
            }
        });

        # Retorna o Quadro completo
        $app->get('/attendance_board/:key', function ($key) use ($app, $db) {
            $key_model = new Key_model($db);
            $attendance_model = new Attendance_board_model($db);

            if ( ! $key_model->_key_exists($key) )
            {
                $app->response()->status(400);
                echo json_encode(array('status' => 0, 'message' => 'Invalid API Key.'));
            } else {
                $app->response()->status(200);
                echo json_encode($attendance_model->get($key));
            }
        });

        # Deleta o Quadro completo
        $app->delete('/attendance_board/:key', function ($key) use ($app, $db) {
            $key_model = new Key_model($db);
            $attendance_model = new Attendance_board_model($db);

            if ( ! $key_model->_key_exists($key) )
            {
                $app->response()->status(400);
                echo json_encode(array('status' => 0, 'message' => 'Invalid API Key.'));
            } else {
                if($attendance_model->delete($key) && $key_model->_delete_key($key))
                {
                    $app->response()->status(200);
                    echo json_encode(array('status' => 1, 'message' => 'Attendance board deleted'));
                } else {
                    $app->response()->status(500);
                    echo json_encode(array('status' => 0, 'message' => 'Internal Server Error'));
                }
            }
        });

        # Atualiza uma presença
        $app->put('/attendance_board/:key/attendance/:attendance_id', function ($key, $attendance_id) use ($app, $db) {
            $key_model = new Key_model($db);
            $attendance_model = new Attendance_board_model($db);

            if ( ! $key_model->_key_exists($key) )
            {
                $app->response()->status(400);
                echo json_encode(array('status' => 0, 'message' => 'Invalid API Key.'));
            } else {
                $string_json = $app->request()->put('data');
                $json = json_decode($string_json);
                
                if ($json == NULL) {
                    $app->response()->status(400);
                    echo json_encode(array('status' => 0, 'message' => 'You need to provide a valid JSON named "data".'));
                } else if (!isset($json->attended)) {
                    $app->response()->status(400);
                    echo json_encode(array('status' => 0, 
                        'message' => 'Your JSON needs to have an "attended" element. Example:{"attended":1}'
                    ));
                } else {
                    $data['key'] = $key;
                    $data['attendance_id'] = $attendance_id;
                    $data['attended'] = $json->attended;

                    if($attendance_model->updateAttendance($data))
                    {
                        $app->response()->status(200);
                        echo json_encode(array('status' => 1, 'message' => 'Attendance updated'));
                    } else {
                        $app->response()->status(400);
                        echo json_encode(array('status' => 0, 'message' => 'Internal Server Error'));
                    }
                }
            }
        });

        # Retorna as presenças de um usuário
        $app->get('/attendance_board/:key/user/:user_id', function ($key, $user_id) use ($app, $db) {
            $key_model = new Key_model($db);
            $attendance_model = new Attendance_board_model($db);

            if ( ! $key_model->_key_exists($key) )
            {
                $app->response()->status(400);
                echo json_encode(array('status' => 0, 'message' => 'Invalid API Key.'));
            } else {
                $attendances = $attendance_model->getUserAttendances($key, $user_id);
                if (!$attendances) {
                    $app->response()->status(400);
                    echo json_encode(array('status' => 0, 'message' => 'Invalid user_id.'));
                } else {
                    $app->response()->status(200);
                    echo json_encode($attendances);
                }
                
            }
        });
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

        # Atualiza uma nota
        $app->put('/score_board/:key/score/:score_id', function ($key, $score_id) use ($app, $db) {
            $key_model = new Key_model($db);
            $score_board_model = new Score_board_model($db);

            if ( ! $key_model->_key_exists($key) )
            {
                $app->response()->status(400);
                echo json_encode(array('status' => 0, 'message' => 'Invalid API Key.'));
            } else {
                $string_json = $app->request()->put('data');
                $json = json_decode($string_json);
                
                if ($json == NULL) {
                    $app->response()->status(400);
                    echo json_encode(array('status' => 0, 'message' => 'You need to provide a valid JSON named "data".'));
                } else if (!isset($json->score)) {
                    $app->response()->status(400);
                    echo json_encode(array('status' => 0, 
                        'message' => 'Your JSON need to have a "score". Example:{"score":10.0}'
                    ));
                } else {
                    $data['key'] = $key;
                    $data['score_id'] = $score_id;
                    $data['score'] = $json->score;

                    if($score_board_model->updateScore($data))
                    {
                        $app->response()->status(200);
                        echo json_encode(array('status' => 1, 'message' => 'Score updated'));
                    } else {
                        $app->response()->status(400);
                        echo json_encode(array('status' => 0, 'message' => 'Internal Server Error'));
                    }
                }
            }
        });

        # Retorna as notas de um usuário
        $app->get('/score_board/:key/user/:user_id', function ($key, $user_id) use ($app, $db) {
            $key_model = new Key_model($db);
            $score_board_model = new Score_board_model($db);

            if ( ! $key_model->_key_exists($key) )
            {
                $app->response()->status(400);
                echo json_encode(array('status' => 0, 'message' => 'Invalid API Key.'));
            } else {
                $scores = $score_board_model->getUserScores($key, $user_id);
                if (!$scores) {
                    $app->response()->status(400);
                    echo json_encode(array('status' => 0, 'message' => 'Invalid user_id.'));
                } else {
                    $app->response()->status(200);
                    echo json_encode($scores);
                }
                
            }
        });

        # Deleta um usuário e suas notas
        $app->delete('/score_board/:key/user/:user_id', function ($key, $user_id) use ($app, $db) {
            $key_model = new Key_model($db);
            $score_board_model = new Score_board_model($db);

            if ( ! $key_model->_key_exists($key) )
            {
                $app->response()->status(400);
                echo json_encode(array('status' => 0, 'message' => 'Invalid API Key.'));
            } else {
                if (!$score_board_model->deleteUser($key, $user_id)) {
                    $app->response()->status(400);
                    echo json_encode(array('status' => 0, 'message' => 'Invalid user_id.'));
                } else {
                    $app->response()->status(200);
                    echo json_encode(array('status' => 1, 'message' => 'User data deleted.'));
                }
                
            }
        });

        # Adiciona um usuário ao Quadro de Notas com nota 0 em todas as atividades existentes
        $app->post('/score_board/:key/user/:user_id', function ($key, $user_id) use ($app, $db) {
            $key_model = new Key_model($db);
            $score_board_model = new Score_board_model($db);

            if ( ! $key_model->_key_exists($key) )
            {
                $app->response()->status(400);
                echo json_encode(array('status' => 0, 'message' => 'Invalid API Key.'));
            } else {
                $score_board_model->insertUser($key, $user_id);
                $app->response()->status(200);
                echo json_encode(array('status' => 1, 'message' => 'User added in all activities with score 0.'));                
            }
        });

        # Retorna os IDs dos usuários existentes
        $app->get('/score_board/:key/users', function ($key) use ($app, $db) {
            $key_model = new Key_model($db);
            $score_board_model = new Score_board_model($db);

            if ( ! $key_model->_key_exists($key) )
            {
                $app->response()->status(400);
                echo json_encode(array('status' => 0, 'message' => 'Invalid API Key.'));
            } else {
                $app->response()->status(200);
                echo json_encode($score_board_model->getUsers($key));                
            }
        });
        // Serviço de Notas - Fim

    });

});


$app->run();

?>