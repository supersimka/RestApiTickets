<?php

declare(strict_types=1);

namespace App\Application\Actions\Ticket;

use Psr\Http\Message\ResponseInterface as Response;
use FaaPz\PDO\Clause;

class AddTicketAction extends ticketAction
{
    protected function action(): Response
    {
      //полученные параметры
      $paramsString = $this->resolveArg('params');
      $params = explode('/',$paramsString);

      //логин, имя, заголовок, текст

       if(count($params) != 4) $answer = 'Неверное переданные параметры запроса!';

        $login = trim($this->clean($params[0]));
        $name = trim($this->clean($params[1]));
        $title = $this->clean($params[2]);
        $comment = $this->clean($params[3]);

        //транзакция
        try {
          	$this->pdo->beginTransaction();

            //вносим пользователя
            $statement = $this->pdo->insert(['login', 'name'])->into('user')->values($login, $name);
            $statement = $statement->execute();
            $creator_id = $this->pdo->lastInsertId('id');

          	//вносим тикет
          	if (!empty($creator_id)) {

              $statement = $this->pdo
                ->insert(['creator_id', 'in_work_user_id','title','status','created_at'])
                ->into('ticket')
                ->values($creator_id, '0', $title, 'new', date('Y-m-d H:i:s'));

              $statement = $statement->execute();
              $ticket_id = $this->pdo->lastInsertId('id');
          	}

            //вносим комментарий
            if (!empty($ticket_id)) {

              $statement = $this->pdo
                ->insert(['ticket_id', 'author_id','comment','created_at'])
                ->into('ticket_comment')
                ->values($ticket_id, $creator_id, $comment, date('Y-m-d H:i:s'));

              $statement = $statement->execute();
          	}

          	$this->pdo->commit();

            $answer = 'Тикет успешно создан!';

            //логируем
            $this->logger->info("Успешное создание тикета");

          } catch (\PDOException $e) {

          	$this->pdo->rollBack();

            //логируем
            $this->logger->info("Ошибка внесения тикета $e->getMessage()");

          	$answer = 'Произошла ошибка: '.$e->getMessage();
          }

      return $this->respondWithData($answer);
    }

}
