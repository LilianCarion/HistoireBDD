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
        $sql = 'SELECT content, id_message_next FROM choices WHERE message_id = :id';
        $stmt = $this->connection->executeQuery($sql, ['id' => $id]);
        $result = $stmt->fetchAllAssociative();
        if (!$result) {
            return new Response('Message non trouvé', 404);
        }
        return $result;
    }

    #[Route('/story/{id}')]
    public function displayMessage(int $id)
    {
        $this->updatePath($id);
        $this->message = $this->getMessage($id);
        $this->choices = $this->getChoices($id);

        return $this->render('story/index.html.twig', [
            'message' => $this->message,
            'choices' => $this->choices
        ]);
    }

    public function updatePath(int $id)
    {
        $sql = 'UPDATE path set path = :id WHERE id = 1';
        $this->connection->executeQuery($sql, ['id' => $id]);
    }
    #[Route('/back')]
    public function back() {
        $sql = "UPDATE path 
                SET path = SUBSTRING_INDEX(path, ';', GREATEST(1, LENGTH(path) - LENGTH(REPLACE(path, ';', ''))))
                WHERE id = 1;";
        $this->connection->executeQuery($sql);

        $sql = "SELECT SUBSTRING_INDEX(path, ';', -1) as last_segment FROM path WHERE id = 1";
        $lastSegment = $this->connection->fetchOne($sql);
        $this->message = $this->getMessage($lastSegment);
        $this->choices = $this->getChoices($lastSegment);

        return $this->render('story/index.html.twig', [
            'message' => $this->message,
            'choices' => $this->choices
        ]);
    }
}
