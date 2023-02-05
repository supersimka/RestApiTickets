<?php

declare(strict_types=1);

namespace App\Application\Actions\Ticket;

use Psr\Http\Message\ResponseInterface as Response;
use FaaPz\PDO\Clause;
use FaaPz\PDO\Clause\Join;
use FaaPz\PDO\Clause\Raw;

class ListCommentsAction extends ticketAction
{ 
    protected function action(): Response
    {
        $ticket_id = (int) $this->resolveArg('ticket_id');

        //запрос к БД
        $statement = $this->pdo
          ->select()
          ->from('ticket_comment')
          ->join(new Join("user", new Clause\Conditional("ticket_comment.author_id",  "=", new Raw("user.id")), "LEFT OUTER"))
          ->where(new Clause\Conditional("ticket_comment.ticket_id", "=", $ticket_id));

        //обрабатываем запрос
        $statement = $statement->execute();
        $comments = $statement->fetchAll();

        //логируем
        $this->logger->info("Просмотрен список комментариев");

        return $this->respondWithData($comments);
    }

}
