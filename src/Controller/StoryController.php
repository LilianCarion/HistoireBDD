<?php

namespace App\Controller;

use http\Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\DBAL\Connection;


class StoryController extends AbstractController
{
    private Connection $connection;
    private $message;
    private $choices;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->message = $this->getMessage(1);
        $this->choices = $this->getChoices(1);
    }
    #[Route('/story', name: 'app_story')]
    public function index(): Response
    {
        return $this->render('story/index.html.twig', [
            'message' => $this->message,
            'choices' => $this->choices
        ]);
    }

    public function getMessage(int $id): Response|string
    {
        $sql = 'SELECT content FROM messages WHERE id = :id';
        $stmt = $this->connection->executeQuery($sql, ['id' => $id]);
        $result = $stmt->fetchAssociative();
        if (!$result) {
            return new Response('Message non trouvé', 404);
        }
        return $result['content'];
    }

    public function getChoices(int $id): array | Response
    {
        $sql = 'SELECT content, orderM FROM choices WHERE message_id = :id';
        $stmt = $this->connection->executeQuery($sql, ['id' => $id]);
        $result = $stmt->fetchAllAssociative();
        if (!$result) {
            return new Response('Message non trouvé', 404);
        }
        return $result;
    }
}
