<?php

declare(strict_types=1);

namespace App\Application\Actions\Ticket;

use Psr\Http\Message\ResponseInterface as Response;
use FaaPz\PDO\Clause;

class UpdateTicketAction extends ticketAction
{
    protected function action(): Response
    {
      //полученные параметры
      $id = (int) $this->resolveArg('id');
      $in_work_user_id = (int) $this->resolveArg('in_work_user_id');
      $status = (string) $this->resolveArg('status');

      if(empty($id) || empty($in_work_user_id) || empty($status)) return $this->respondWithData('Не все параметры заполнены!');

        //запрос
        try {
            //берем тикет в работу
            $statement = $this->pdo->update(['in_work_user_id	' => $in_work_user_id, 'status' => $status])
                      ->table('ticket')
                      ->where(new Clause\Conditional("id", "=", $id));
            $statement = $statement->execute();

            $answer = 'Тикет успешно взят в работу!';

            //логируем
            $this->logger->info("Тикет успешно взят в работу");

          } catch (Exception $e)
          {
            //логируем
            $this->logger->info("Ошибка изменения тикета $e->getMessage()");

            $answer = 'Произошла ошибка: '.$e->getMessage();
          }

      return $this->respondWithData($answer);
    }

}
