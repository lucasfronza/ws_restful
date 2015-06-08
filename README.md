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
- [ ] POST

/wiki/{key}
- [ ] DELETE
- [ ] GET
- [ ] PUT

#Planejamento Wiki
/wiki
- POST: cria um novo wiki vazio, retornando um 'key'

/wiki/{key}
- DELETE: deleta o wiki
- GET: retorna o wiki
- PUT: atualiza o wiki passando text e datetime
Ex: {"text":"Lorem ipsum", "datetime":"28-06-2015 14:00"}