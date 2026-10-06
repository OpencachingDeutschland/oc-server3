<?php

declare(strict_types=1);

namespace Oc\Menu;

use Knp\Menu\Attribute\AsMenuBuilder;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Oc\Security\Auth;
use Symfony\Contracts\Translation\TranslatorInterface;

// https://symfony.com/bundles/KnpMenuBundle/current/menu_builder_service.html
class MenuGenerator
{
    private FactoryInterface $factory;

    private Auth $auth;

    private TranslatorInterface $translator;

    public function __construct(FactoryInterface $factory, Auth $auth, TranslatorInterface $translator)
    {
        $this->factory = $factory;
        $this->auth = $auth;
        $this->translator = $translator;
    }

    private function addMenuItem(
            ItemInterface $menu,
            string $child,
            string $label,
            string $route,
            string $labelName,
            string $labelValue
    ): void {
        $menu->addChild($child, [
                'label' => $label,
                'route' => $route,
            // 'childOptions' => $event->getChildOptions(), // wozu braucht's das?
        ])->setLabelAttribute($labelName, $labelValue); // TODO: wird für css/html benutzt
    }

    #[AsMenuBuilder(name: 'sideMenu')] // The name is what is used to retrieve the menu
    public function createSideMenu(array $options): ItemInterface {
        $menu = $this->factory->createItem('root');

        $this->addMenuItem($menu, 'menuSearch', $this->translator->trans('Search'), 'app_caches_index', 'icon', 'fas fa-search-location');
        $this->addMenuItem($menu['menuSearch'], 'menuSearchCaches', $this->translator->trans('Search caches'), 'app_caches_index', 'icon', 'fas fa-search-location');
        $this->addMenuItem($menu['menuSearch'], 'menuSearchUsers', $this->translator->trans('Search users'), 'app_user_index', 'icon', 'fas fa-search-location');
        $this->addMenuItem($menu, 'menuHide', $this->translator->trans('Hide'), 'app_cache_new', 'icon', 'fas fa-hiking');

        $this->addMenuItem($menu, 'menuLiveMap', $this->translator->trans('Live Map'), 'app_livemap', 'icon', 'fas fa-map-marked-alt');


        $this->addMenuItem($menu, 'menuNews', $this->translator->trans('News'), '', 'icon', 'fas fa-newspaper');
        $this->addMenuItem($menu['menuNews'], $this->translator->trans('menuNewsBlog'), 'Blog & OC-Talk', '', 'icon', 'fas fa-newspaper');
        $this->addMenuItem($menu['menuNews'], $this->translator->trans('menuNewsEvents'), 'Events', '', 'icon', 'fas fa-newspaper');
        $this->addMenuItem($menu['menuNews'], $this->translator->trans('menuNewsLogpictures'), 'Log pictures', '', 'icon', 'fas fa-newspaper');
        $this->addMenuItem($menu['menuNews'], $this->translator->trans('menuNewsRecommendations'), 'Recommendations', '', 'icon', 'fas fa-newspaper');
        $this->addMenuItem($menu['menuNews'], $this->translator->trans('menuNewsHidesGermany'), 'Hides in Germany', '', 'icon', 'fas fa-newspaper');
        $this->addMenuItem($menu['menuNews'], $this->translator->trans('menuNewsHidesWorld'), 'Hides worldwide', '', 'icon', 'fas fa-newspaper');
        $this->addMenuItem($menu['menuNews'], $this->translator->trans('menuNewsLogs'), 'Logs', '', 'icon', 'fas fa-newspaper');

        $this->addMenuItem($menu, 'menuBookmarks', $this->translator->trans('Bookmark lists'), '', 'icon', 'fas fa-bookmark');
        $this->addMenuItem($menu['menuBookmarks'], $this->translator->trans('menuBookmarksWatch'), 'Watched caches', '', 'icon', 'fas fa-bookmark');
        $this->addMenuItem($menu['menuBookmarks'], $this->translator->trans('menuBookmarksOwnLists'), 'Bookmark lists', '', 'icon', 'fas fa-bookmark');
        $this->addMenuItem($menu['menuBookmarks'], $this->translator->trans('menuBookmarksPublicLists'), 'Public bookmark lists', '', 'icon', 'fas fa-bookmark');
        $this->addMenuItem($menu['menuBookmarks'], $this->translator->trans('menuBookmarksRecommendations'), 'Recommendations', '', 'icon', 'fas fa-bookmark');
        $this->addMenuItem($menu['menuBookmarks'], $this->translator->trans('menuBookmarksSearches'), 'Saved searches', '', 'icon', 'fas fa-bookmark');
        $this->addMenuItem($menu['menuBookmarks'], $this->translator->trans('menuBookmarksIgnore'), 'Ignore list', '', 'icon', 'fas fa-bookmark');

        $this->addMenuItem($menu, 'menuFieldNotes', $this->translator->trans('Field Notes'), '', 'icon', 'fas fa-clipboard');

        $this->addMenuItem($menu, 'menuContact', $this->translator->trans('Contact'), '', 'icon', 'fas fa-envelope-open-text');

        $this->addMenuItem($menu, 'menuOC', $this->translator->trans('OC.de & legal'), '', 'icon', 'fas fa-chart-line');
        $this->addMenuItem($menu['menuOC'], 'menuOCAbout', $this->translator->trans('About'), '', 'icon', 'fas fa-map-marker-alt');
        $this->addMenuItem($menu['menuOC'], 'menuOCLegal', $this->translator->trans('Data license'), '', 'icon', 'fas fa-map-marker-alt');
        $this->addMenuItem($menu['menuOC'], 'menuOCPrivacy', $this->translator->trans('Privacy policy'), '', 'icon', 'fas fa-map-marker-alt');
        $this->addMenuItem($menu['menuOC'], 'menuOCImprint', $this->translator->trans('Imprint'), '', 'icon', 'fas fa-map-marker-alt');
        $this->addMenuItem($menu['menuOC'], 'menuOCTOU', $this->translator->trans('Terms of use'), '', 'icon', 'fas fa-map-marker-alt');
        $this->addMenuItem($menu['menuOC'], 'menuOCOCOnly81', $this->translator->trans('OCOnly-81'), 'app_oconly81_index', 'icon', 'fas fa-map-marker-alt');

        if ($this->auth->isGranted('ROLE_SUPPORT_TRAINEE')) {
            $this->addMenuItem($menu, 'menuSupport', $this->translator->trans('Support Center'), '', 'icon', 'fas fa-user-shield');
            $this->addMenuItem($menu['menuSupport'], 'menuSupportReported', $this->translator->trans('Reported caches'), 'backoffice_support_reported_caches', 'icon', 'fas fa-flag');
            $this->addMenuItem($menu['menuSupport'], 'menuSupportSearch',   $this->translator->trans('Search users'),    'app_user_index',                  'icon', 'fas fa-search');
            if ($this->auth->isGranted('ROLE_TEAM')) {
                $this->addMenuItem($menu['menuSupport'], 'menuSupportRoles',     $this->translator->trans('DEV Roles'),       'backoffice_roles_index',             'icon', 'fas fa-user-tag');
            }
        }

        // Login/Logout/username are rendered by the navbar template (right-side dropdown)
        // based on app.user — no static menu items here.

        return $menu;
    }
}
