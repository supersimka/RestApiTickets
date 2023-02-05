<?php

declare(strict_types=1);

namespace App\Application\Actions\Ticket;

use Psr\Http\Message\ResponseInterface as Response;
use FaaPz\PDO\Clause;

class DeleteTicketAction extends ticketAction
{

    protected function action(): Response
    {
      //полученные параметры
      $id = (int) $this->resolveArg('id');

        //транзакция
        try {
           $this->pdo->beginTransaction();

            //удаляем комментарии
            $statement = $this->pdo->delete()->from('ticket_comment')->where(new Clause\Conditional("ticked_id", "=", $id));
            $statement = $statement->execute();

            //удаляем тикет
            $statement = $this->pdo->delete()->from('ticket')->where(new Clause\Conditional("id", "=", $id));
            $statement = $statement->execute();

           $this->pdo->commit();

            $answer = 'Тикет успешно удален!';

            //логируем
            $this->logger->info("Успешное удаление тикета");

          } catch (\PDOException $e) {

           $this->pdo->rollBack();

            //логируем
            $this->logger->info("Ошибка удаления тикета $e->getMessage()");

           $answer = 'Произошла ошибка: '.$e->getMessage();
          }

      return $this->respondWithData($answer);
    }

}
