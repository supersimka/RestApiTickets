<?php

declare(strict_types=1);

namespace App\Application\Actions\Ticket;

use Psr\Http\Message\ResponseInterface as Response;
use FaaPz\PDO\Clause;
use FaaPz\PDO\Clause\Join;
use FaaPz\PDO\Clause\Raw;

class ListTicketAction extends ticketAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        //запрос к БД
        $statement = $this->pdo
          ->select()
          ->from('ticket')
          ->join(new Join("user", new Clause\Conditional("ticket.creator_id",  "=", new Raw("user.id")), "LEFT OUTER"));

        //если указан id сотрудника
        if(!empty($this->resolveArg('in_work_user_id')))
        {
          $in_work_user_id = (int) $this->resolveArg('in_work_user_id');
          $statement = $statement->where(new Clause\Conditional("in_work_user_id", "=", $in_work_user_id));
        }

        //если указан статус тикета
        if(!empty($this->resolveArg('status')))
        {
          $status = (string) $this->resolveArg('status');
          $statement = $statement->where(new Clause\Conditional("status", "=", $status));
        }

        //обрабатываем запрос
        $statement = $statement->execute();
        $tickets = $statement->fetchAll();

        //логируем
        $this->logger->info("Tickets list was viewed.");

        return $this->respondWithData($tickets);
    } 
}
