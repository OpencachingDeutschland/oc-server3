<?php

declare(strict_types=1);

namespace Oc\Components\Subscriber;

use KevinPapst\AdminLTEBundle\Event\KnpMenuEvent;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 *
 */
class MenuSubscriber implements EventSubscriberInterface
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * MenuSubscriber constructor.
     *
     * @param Security $security
     * @param TranslatorInterface $translator
     */
    public function __construct(Security $security, TranslatorInterface $translator)
    {
        $this->security = $security;
        $this->translator = $translator;
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents()
    : array
    {
        return [
            KnpMenuEvent::class => ['onSetupMenu', 100],
        ];
    }

    /**
     */
    private function addMenuItem(
        ItemInterface $menu,
        string $child,
        string $label,
        string $route,
        string $labelName,
        string $labelValue
    ) {
        $menu->addChild($child, [
            'label' => $label,
            'route' => $route,
            // 'childOptions' => $event->getChildOptions(), // wozu braucht's das?
        ])->setLabelAttribute($labelName, $labelValue);
    }

    /**
     * @param KnpMenuEvent $event
     */
    public function onSetupMenu(KnpMenuEvent $event)
    {
        $menu = $event->getMenu();

        // TODO: Routen und Icons bei den meisten Menüeinträgen noch anpassen.
        // https://symfony.com/bundles/KnpMenuBundle/current/index.html

        $this->addMenuItem($menu, 'menuSearch', $this->translator->trans('Search'), 'app_caches_index', 'icon', 'fas fa-search-location');
        $this->addMenuItem($menu['menuSearch'], 'menuSearchCaches', $this->translator->trans('Search caches'), 'app_caches_index', 'icon', 'fas fa-search-location');
        $this->addMenuItem($menu['menuSearch'], 'menuSearchUsers', $this->translator->trans('Search users'), 'app_user_index', 'icon', 'fas fa-search-location');

        $this->addMenuItem($menu, 'menuHide', $this->translator->trans('Hide'), '', 'icon', 'fas fa-hiking');

        $this->addMenuItem($menu, 'menuMap', $this->translator->trans('Map'), 'app_map_show', 'icon', 'fas fa-map');

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

        $this->addMenuItem($menu, 'menuLogout', $this->translator->trans('Logout'), 'app_security_logout', 'icon', 'fas fa-door-open');
    }
}
