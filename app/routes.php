<?php

declare(strict_types=1);
 
use App\Application\Actions\Ticket\ListTicketAction;
use App\Application\Actions\Ticket\ViewTicketAction;
use App\Application\Actions\Ticket\AddTicketAction;
use App\Application\Actions\Ticket\DeleteTicketAction;
use App\Application\Actions\Ticket\UpdateTicketAction;
use App\Application\Actions\Ticket\AddCommentAction;
use App\Application\Actions\Ticket\ListCommentsAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Пожалуйста, обратитесь к ресурсу!');
        return $response;
    });

    //добавление тикета
    $app->post('/add_ticket[/{params:.*}]', AddTicketAction::class);

    //удаление тикета
    $app->delete('/delete_ticket/{id}', DeleteTicketAction::class);

    //изменение тикета
    $app->put('/update_ticket/{id}/{in_work_user_id}/{status}', UpdateTicketAction::class);

    //получение списка тикетов
    $app->get('/ticket[/{in_work_user_id}[/{status}]]', ListTicketAction::class);

    //добавление комментария
    $app->post('/add_comment/{ticket_id}/{creator_id}/{text}', AddCommentAction::class);

    //получение списка комментариев
    $app->get('/comments/{ticket_id}', ListCommentsAction::class);
};
