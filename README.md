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
/wiki
- [x] POST

/wiki/{key}
- [x] DELETE
- [x] GET
- [x] PUT

#Planejamento Wiki
/wiki
- POST: cria um novo wiki vazio, retornando um 'key'

/wiki/{key}
- DELETE: deleta o wiki
- GET: retorna o wiki
- PUT: atualiza o wiki passando 'text'