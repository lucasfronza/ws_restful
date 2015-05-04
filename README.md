ws_restful
=============

#Lista de Tarefas
/score_board
- [x] POST

/score_board/{key}
- [x] DELETE
- [x] GET
- [x] POST

/score_board/{key}/score/{score_id}
- [x] PUT

/score_board/{key}/user/{user_id}
- [x] GET
- [x] DELETE
- [x] POST

/score_board/{key}/users
- [x] GET

#Planejamento Quadro de Notas

/score_board
    - POST: cria um novo quadro de notas, retornando um 'key'

/score_board/{key}
    - DELETE: deleta um quadro de notas
    - GET: retorna o quadro com todas as notas
    - POST: insere uma lista de notas com os IDs dos usuarios e um titulo para a nota(ex: Prova 1)
        Ex: {title:'P1', scores:
                [
                    {user_id:8, score:10},
                    {user_id:3, score:9.5},
                    {user_id:12, score:9.7}
                ]
            }

/score_board/{key}/score/{score_id}
    - PUT: atualiza a nota

/score_board/{key}/user/{user_id}
    - GET: retorna as notas de um determinado usuario
    - DELETE: deleta um usuario do quadro de notas
    - POST: adiciona um usuario ao quadro de notas

/score_board/{key}/users
    - GET: retorna os IDs dos usuarios existentes

