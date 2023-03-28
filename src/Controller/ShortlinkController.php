<?php

namespace App\Controller;

use App\Entity\ShortUri;
use App\Repository\ShortUriRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShortlinkController extends BaseController
{
    public function redirectShortLink(string $link, ShortUriRepository $repository)
    {
        $link = trim($link);
        if (!$link) {
            throw new BadRequestException('No any link');
        }

        $crc32 = $repository::crc32($link);
        $rows = $repository->findByShortUriCrc($crc32);
        if (empty($rows)) {
            throw new NotFoundHttpException('Link not found');
        }

        $shortUri = reset($rows);

        return $this->redirect(
            $shortUri->getUri(),
            $shortUri->getStatus()
        );
    }

    public function generate(Request $request, ShortUriRepository $repository): Response
    {
        $response = new JsonResponse();
        $link = trim($this->getRequestJson('link', $request));
        $status = intval($this->getRequestJson('status', $request)) ?: 301;

        if (!$link) {
            throw new BadRequestException('No any link');
        }

        $this->logger->info('Got link for generating', ['link' => $link]);

        $crc32 = $repository::crc32($link);
        $rows = $repository->findByUriCrc($crc32);

        if (!empty($rows)) {
            return $response->setData($rows[0]);
        }

        $shortUri = $repository->generateShortUri();
        $shortUriCrc32 = $repository::crc32($shortUri);
        $this->logger->info('Short uri generated', [
            'short_link' => $shortUri
        ]);

        $uri = (new ShortUri)
            ->setUri($link)
            ->setUriCrc($crc32)
            ->setShortUri($shortUri)
            ->setShortUriCrc($shortUriCrc32)
            ->setStatus($status);

        $repository->save($uri, true);

        if ($uri->getId()) {
            $this->logger->info('Link saved', [
                'link' => $uri->getUri(),
                'short_link' => $uri->getShortUri()
            ]);

            return $response->setData($uri);
        }

        throw new \Exception('Cant save link');
    }
}