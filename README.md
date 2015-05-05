ws_restful
=============
Serviços Web educacionais

#Lista de Serviços
- [x] Quiz
- [x] Quadro de Notas
- [ ] Quadro de Presença

#Lista de Tarefas
/attendance_board
- [ ] POST

/attendance_board/{key}
- [ ] DELETE
- [ ] GET
- [ ] POST

/attendance_board/{key}/score/{score_id}
- [ ] PUT

/attendance_board/{key}/user/{user_id}
- [ ] GET
- [ ] DELETE
- [ ] POST

/attendance_board/{key}/users
- [ ] GET

#Planejamento Quadro de Presença

/attendance_board
    - POST: cria um novo quadro de presença, retornando um 'key'

/attendance_board/{key}
    - DELETE: deleta um quadro de presença
    - GET: retorna o quadro completo
    - POST: insere uma lista presença com os IDs dos usuarios e um titulo para a atividade(ex: Aula Extra, 24-05-2015)
        Ex: {"title":"Aula Extra 28-03-2015", "scores":
                [
                    {"user_id":8, "attended":1},
                    {"user_id":3, "attended":0},
                    {"user_id":12, "attended":1}
                ]
            }

/attendance_board/{key}/score/{score_id}
    - PUT: atualiza uma presença

/attendance_board/{key}/user/{user_id}
    - GET: retorna as presenças de um determinado usuario
    - DELETE: deleta um usuario do quadro de presença
    - POST: adiciona um usuario ao quadro de presença

/attendance_board/{key}/users
    - GET: retorna os IDs dos usuarios existentes

