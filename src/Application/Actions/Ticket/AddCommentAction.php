<?php

declare(strict_types=1);

namespace App\Application\Actions\Ticket;

use Psr\Http\Message\ResponseInterface as Response;
use FaaPz\PDO\Clause;
use FaaPz\PDO\Clause\Join;
use FaaPz\PDO\Clause\Raw;

class AddCommentAction extends ticketAction
{
    protected function action(): Response
    {
      //полученные параметры
      $ticket_id = (int) $this->resolveArg('ticket_id');
      $creator_id = (int) $this->resolveArg('creator_id');
      $comment = $this->clean($this->resolveArg('text'));

      if(empty($ticket_id) || empty($comment) || empty($creator_id)) return $this->respondWithData('Не все параметры заполнены!');

      //транзакция
      try {
          $this->pdo->beginTransaction();

          //вносим комментарий
          $statement = $this->pdo
            ->insert(['ticket_id', 'author_id','comment','created_at'])
            ->into('ticket_comment')
            ->values($ticket_id, $creator_id, $comment, date('Y-m-d H:i:s'));

          $statement = $statement->execute();

          //изменяем тикет
          $statement = $this->pdo->update(['in_work_user_id	' => '0', 'status' => 'wait_answer'])
                    ->table('ticket')
                    ->where(new Clause\Conditional("id", "=", $ticket_id));
          $statement = $statement->execute();

          $this->pdo->commit();

          $answer = 'Комментарий успешно создан!';

          //логируем
          $this->logger->info("Успешное создание комментария");

      } catch (Exception $e) {

          $this->pdo->rollBack();

          //логируем
          $this->logger->info("Ошибка внесения комментария $e->getMessage()");

          $answer = 'Произошла ошибка: '.$e->getMessage();
        }

      return $this->respondWithData($answer);
    }
}
