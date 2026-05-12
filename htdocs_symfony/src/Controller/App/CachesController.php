<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Form\CachesFormType;
use Oc\Repository\CachesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CachesController extends AbstractController
{
    public function __construct(
        private CachesRepository $cachesRepository,
        private Connection $connection,
    ) {}

    #[Route("/caches", name: "caches_index")]
    public function cachesController_index(Request $request): Response
    {
        $fetchedCaches = '';

        $form = $this->createForm(CachesFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inputData = $form->getData();
            $fetchedCaches = $this->cachesRepository->getCachesForSearchField($inputData['content_searchfield']);

            if (empty($fetchedCaches)) {
                if (preg_match('/[a-zA-Z0-9]{3,5}/', $inputData['content_searchfield'])
                    && !str_starts_with($inputData['content_searchfield'], 'OC')
                    && !str_starts_with($inputData['content_searchfield'], 'GC')
                ) {
                    $fetchedCaches = $this->cachesRepository->getCachesForSearchFieldWPOnly($inputData['content_searchfield']);
                }
            }
        }

        return $this->render('app/caches/search.html.twig', [
            'cachesForm' => $form->createView(),
            'caches_by_searchfield' => $fetchedCaches,
        ]);
    }

    #[Route("/cache/{wpID}", name: "cache_by_wp_oc_gc")]
    public function detail(string $wpID): Response
    {
        return $this->render('app/caches/detail.html.twig', ['wp' => strtoupper($wpID)]);
    }

    /**
     * @throws Exception
     */
    #[Route("/api/cache/{wp}", name: "api_cache_detail")]
    public function apiDetail(string $wp): JsonResponse
    {
        $wp = strtoupper($wp);

        // Main cache data
        $cache = $this->connection->fetchAssociative(
            'SELECT
                c.cache_id, c.wp_oc, c.name,
                c.latitude, c.longitude,
                c.difficulty / 2 AS difficulty,
                c.terrain / 2 AS terrain,
                c.country, c.date_hidden, c.wp_gc,
                IF(c.logpw != \'\', 1, 0) AS logpw,
                c.needs_maintenance, c.listing_outdated,
                ct.en AS type_name, ct.svg_name,
                cs.name AS size_name,
                cst.name AS status_name,
                u.user_id AS owner_id, u.username AS owner_name,
                IFNULL(sc.found, 0) AS find_count,
                IFNULL(sc.toprating, 0) AS rating_count
             FROM caches c
             JOIN cache_type ct   ON c.type    = ct.id
             JOIN cache_size cs   ON c.size    = cs.id
             JOIN cache_status cst ON c.status = cst.id
             JOIN user u          ON c.user_id = u.user_id
             LEFT JOIN stat_caches sc ON c.cache_id = sc.cache_id
             WHERE c.wp_oc = ?',
            [$wp]
        );

        if (!$cache) {
            return new JsonResponse(['error' => 'Cache not found'], 404);
        }

        $cacheId = (int)$cache['cache_id'];

        // Description (prefer default language, fall back to first available)
        $desc = $this->connection->fetchAssociative(
            'SELECT cd.desc, cd.hint, cd.short_desc, cd.desc_html, cd.language
             FROM cache_desc cd
             WHERE cd.cache_id = ?
             ORDER BY cd.language = ? DESC, cd.language = \'EN\' DESC
             LIMIT 1',
            [$cacheId, strtoupper($cache['country'] ?: 'DE')]
        );

        // Public waypoints
        $waypoints = $this->connection->fetchAllAssociative(
            'SELECT co.latitude, co.longitude, co.description, ct.name AS type_name
             FROM coordinates co
             LEFT JOIN coordinates_type ct ON co.subtype = ct.id
             WHERE co.cache_id = ? AND co.type = 1 AND co.user_id IS NULL
             ORDER BY co.id',
            [$cacheId]
        );

        // Attributes
        $attributes = $this->connection->fetchAllAssociative(
            'SELECT ca.id, ca.name, ca.icon
             FROM caches_attributes cxa
             JOIN cache_attrib ca ON cxa.attrib_id = ca.id
             WHERE cxa.cache_id = ?
             ORDER BY ca.id',
            [$cacheId]
        );

        // Logs (most recent 20)
        $logs = $this->connection->fetchAllAssociative(
            'SELECT cl.id, cl.type, lt.en AS type_name,
                    DATE_FORMAT(cl.date, \'%Y-%m-%d\') AS date,
                    cl.text, cl.text_html,
                    u.username
             FROM cache_logs cl
             JOIN user u      ON cl.user_id = u.user_id
             LEFT JOIN log_types lt ON cl.type = lt.id
             WHERE cl.cache_id = ? AND cl.gdpr_deletion = 0
             ORDER BY cl.date DESC
             LIMIT 20',
            [$cacheId]
        );

        return new JsonResponse([
            'wp'             => $cache['wp_oc'],
            'name'           => $cache['name'],
            'typeName'       => $cache['type_name'],
            'typeSvg'        => $cache['svg_name'],
            'sizeName'       => $cache['size_name'],
            'statusName'     => $cache['status_name'],
            'difficulty'     => (float)$cache['difficulty'],
            'terrain'        => (float)$cache['terrain'],
            'lat'            => (float)$cache['latitude'],
            'lon'            => (float)$cache['longitude'],
            'country'        => $cache['country'],
            'dateHidden'     => $cache['date_hidden'] ? substr($cache['date_hidden'], 0, 10) : '',
            'wpGc'           => $cache['wp_gc'] ?: '',
            'logpw'          => (bool)$cache['logpw'],
            'needsMaintenance' => (bool)$cache['needs_maintenance'],
            'listingOutdated'  => (bool)$cache['listing_outdated'],
            'ownerId'        => (int)$cache['owner_id'],
            'ownerName'      => $cache['owner_name'],
            'findCount'      => (int)$cache['find_count'],
            'ratingCount'    => (int)$cache['rating_count'],
            'desc'           => $desc['desc'] ?? '',
            'shortDesc'      => $desc['short_desc'] ?? '',
            'hint'           => $desc['hint'] ?? '',
            'descHtml'       => (bool)($desc['desc_html'] ?? true),
            'waypoints'      => array_map(fn($w) => [
                'lat'         => (float)$w['latitude'],
                'lon'         => (float)$w['longitude'],
                'typeName'    => $w['type_name'] ?? 'Waypoint',
                'description' => $w['description'] ?? '',
            ], $waypoints),
            'attributes'     => array_map(fn($a) => [
                'id'   => (int)$a['id'],
                'name' => $a['name'],
                'icon' => $a['icon'],
            ], $attributes),
            'logs'           => array_map(fn($l) => [
                'id'       => (int)$l['id'],
                'type'     => (int)$l['type'],
                'typeName' => $l['type_name'] ?? '',
                'date'     => $l['date'],
                'username' => $l['username'],
                'text'     => $l['text'],
                'textHtml' => (bool)$l['text_html'],
            ], $logs),
        ]);
    }
}
