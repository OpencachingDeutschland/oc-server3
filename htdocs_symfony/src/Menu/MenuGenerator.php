<?php

declare(strict_types=1);

namespace Oc\Menu;

use Knp\Menu\Attribute\AsMenuBuilder;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

// https://symfony.com/bundles/KnpMenuBundle/current/menu_builder_service.html
class MenuGenerator
{
    private FactoryInterface $factory;

    private Security $security;

    private TranslatorInterface $translator;

    /**
     * Add any other dependency you need...
     */
    public function __construct(FactoryInterface $factory, Security $security, TranslatorInterface $translator)
    {
        $this->factory = $factory;
        $this->security = $security;
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
        $this->addMenuItem($menu['menuSearch'], 'menuCoordinatesConverter', $this->translator->trans('Identify coordinates format'), 'app_coordinates_format_identify', 'icon', 'fas fa-search-location');

        $this->addMenuItem($menu, 'menuHide', $this->translator->trans('Hide'), '', 'icon', 'fas fa-hiking');

        $this->addMenuItem($menu, 'menuMap', $this->translator->trans('Map'), 'app_map_show', 'icon', 'fas fa-map');
        $this->addMenuItem($menu, 'menuLiveMap', $this->translator->trans('Live Map'), 'app_livemap', 'icon', 'fas fa-map-marked-alt');

        $this->addMenuItem($menu, 'menuDemos', $this->translator->trans('Demos'), '', 'icon', 'fas fa-flask');
        $this->addMenuItem($menu['menuDemos'], 'menuDemosTabulator', $this->translator->trans('Tabulator'), 'app_demo_tabulator', 'icon', 'fas fa-table');
        $this->addMenuItem($menu['menuDemos'], 'menuDemosAgGrid', $this->translator->trans('AG-Grid'), 'app_demo_ag-grid', 'icon', 'fas fa-th');
        $this->addMenuItem($menu['menuDemos'], 'menuDemosSsr', $this->translator->trans('SSR Twig'), 'app_demo_ssr', 'icon', 'fas fa-server');

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

        $this->addMenuItem($menu, 'menuProfile', $this->translator->trans('Profile'), '', 'icon', 'fas fa-address-card');
        $this->addMenuItem($menu['menuProfile'], $this->translator->trans('menuProfileOwnFounds'), 'Own founds', '', 'icon', 'fas fa-address-card');
        $this->addMenuItem($menu['menuProfile'], $this->translator->trans('menuProfileOwnLogspictures'), 'Own log pictures', '', 'icon', 'fas fa-address-card');
        $this->addMenuItem($menu['menuProfile'], $this->translator->trans('menuProfileOwnHides'), 'Own hides', '', 'icon', 'fas fa-address-card');
        $this->addMenuItem($menu['menuProfile'], $this->translator->trans('menuProfileAdoptions'), 'Adoptions', '', 'icon', 'fas fa-address-card');
        $this->addMenuItem($menu['menuProfile'], $this->translator->trans('menuProfileOwnCaches'), 'Own caches', '', 'icon', 'fas fa-address-card');
        $this->addMenuItem($menu['menuProfile'], $this->translator->trans('menuProfilePublic'), 'Public profile', '', 'icon', 'fas fa-address-card');
        $this->addMenuItem($menu['menuProfile'], $this->translator->trans('menuProfileBanner'), 'Banner', '', 'icon', 'fas fa-user');

        $this->addMenuItem($menu, 'menuSettings', $this->translator->trans('Settings'), '', 'icon', 'fas fa-cogs');
        $this->addMenuItem($menu['menuSettings'], $this->translator->trans('menuSettingsProfile'), 'Profile', '', 'icon', 'fas fa-cogs');
        $this->addMenuItem($menu['menuSettings'], $this->translator->trans('menuSettingsAPI'), 'API', '', 'icon', 'fas fa-cogs');
        $this->addMenuItem($menu['menuSettings'], $this->translator->trans('menuSettingsCookies'), 'Cookies', '', 'icon', 'fas fa-cogs');

        $this->addMenuItem($menu, 'menuContact', $this->translator->trans('Contact'), '', 'icon', 'fas fa-envelope-open-text');

        $this->addMenuItem($menu, 'menuOC', $this->translator->trans('OC.de & legal'), '', 'icon', 'fas fa-chart-line');
        $this->addMenuItem($menu['menuOC'], 'menuOCAbout', $this->translator->trans('About'), '', 'icon', 'fas fa-map-marker-alt');
        $this->addMenuItem($menu['menuOC'], 'menuOCLegal', $this->translator->trans('Data license'), '', 'icon', 'fas fa-map-marker-alt');
        $this->addMenuItem($menu['menuOC'], 'menuOCPrivacy', $this->translator->trans('Privacy policy'), '', 'icon', 'fas fa-map-marker-alt');
        $this->addMenuItem($menu['menuOC'], 'menuOCImprint', $this->translator->trans('Imprint'), '', 'icon', 'fas fa-map-marker-alt');
        $this->addMenuItem($menu['menuOC'], 'menuOCTOU', $this->translator->trans('Terms of use'), '', 'icon', 'fas fa-map-marker-alt');
        $this->addMenuItem($menu['menuOC'], 'menuOCOCOnly81', $this->translator->trans('OCOnly-81'), 'app_oconly81_index', 'icon', 'fas fa-map-marker-alt');

        if ($this->security->isGranted('ROLE_TEAM')) {
            $this->addMenuItem($menu, 'menuSupport', $this->translator->trans('Support Center'), 'backend_support_reported_caches', 'icon', 'fas fa-user-shield');

            $this->addMenuItem($menu, 'menuKitchensink', $this->translator->trans('DEV Kitchensink'), 'app_kitchensink_index', 'icon', 'fab fa-css3');

            $this->addMenuItem($menu, 'menuRoles', $this->translator->trans('DEV Roles'), 'backend_roles_index', 'icon', 'fas fa-user-shield');
        }

        $this->addMenuItem($menu, 'menuLogin', $this->translator->trans('Login'), 'app_security_login', 'icon', 'fas fa-door-open');
        $this->addMenuItem($menu, 'menuLogout', $this->translator->trans('Logout'), 'app_security_logout', 'icon', 'fas fa-door-open');

        return $menu;
    }
}
