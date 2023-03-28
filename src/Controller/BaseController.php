<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    protected array $body;

    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function getRequestJson(string $field, Request $request)
    {
        if (empty($this->body)) {
            $this->body = json_decode($request->getContent(), true);
        }

        if (!array_key_exists($field, $this->body)) {
            return '';
        }

        return (string) $this->body[$field] ?? '';
    }
}