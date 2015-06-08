Repositório ws_restful
=============
Serviços Web educacionais

#Lista de Serviços
- [x] Quiz
- [x] Quadro de Notas
- [x] Quadro de Presença
- [x] Quadro de Avisos
- [ ] Wiki

#Lista de Tarefas
/notice_board
- [x] POST

/notice_board/{key}
- [x] DELETE
- [x] GET
- [x] POST

/notice_board/{key}/notice/{notice_id}
- [x] PUT
- [x] DELETE

#Planejamento Quadro de Avisos
/notice_board
- POST: cria um novo quadro de avisos, retornando um 'key'

/notice_board/{key}
- DELETE: deleta um quadro de avisos
- GET: retorna o quadro completo
- POST: insere uma aviso com titulo, aviso e data/hora
Ex: {"title":"Aula Extra 28-03-2015", "notice":"Lorem ipsum", "datetime":"28-06-2015 14:00"}

/notice_board/{key}/notice/{notice_id}
- PUT: atualiza um aviso
- DELETE: deleta um aviso