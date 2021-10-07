<?php
declare(strict_types=1);

namespace App\Menu;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Security;

final class MenuBuilder
{
    /**
     * @var FactoryInterface
     */
    private FactoryInterface $factory;

    /**
     * @var MenuRepository
     */
    private MenuRepository $repository;

    /**
     * @var Security
     */
    private Security $security;

    public function __construct(FactoryInterface $factory, MenuRepository $menuRepository, Security $security)
    {
        $this->factory = $factory;
        $this->repository = $menuRepository;
        $this->security = $security;
    }

    /**
     * @return ItemInterface
     * @throws \ReflectionException
     */
    public function mainMenu(): ItemInterface
    {
        // create menu root
        $root = $this->factory->createItem('root');
        $root->setChildrenAttribute('class', 'navbar-nav'); // set <ul> attribute -- mx-auto mb-2 mb-lg-0

        // get menu labels
        $menuLabels = $this->repository->findAll();

        // sort menu labels
        $this->mergeSort($menuLabels, 0, count($menuLabels)-1);

        // requires items to be sorted - to correctly find last in first row
        $this->setMenuPrefix($menuLabels);

        /* @var $label Menu */
        foreach ($menuLabels as $label)
        {
            if(!$label instanceof Menu) {
                continue;
            }

            // create new menu label
            $newMenuLabel = $this->factory->createItem($label->getCategory(), [
                'route' => strtolower((new \ReflectionClass($label))->getShortName()),
                'routeParameters' => [
                    'menu' => $this->generateRoute($label)
                ]
            ]);

            // fix menu label parent styling: add dropdown properties etc
            $menuLabelParent = $this->getMenuLabelParent($root, $label);    // return root if label is not children

            if($label->getParent() !== null) {
                $this->styleMenuLabelParent($menuLabelParent, $label, );

                $newMenuLabel->setAttribute('class', 'dropdown-item');  // <li>
                $newMenuLabel->setLinkAttribute('class', 'nav-link');   // <a>
            } else {
                $newMenuLabel->setAttribute('class', 'nav-item');
                $newMenuLabel->setLinkAttribute('class', 'nav-link');
            }

            // save element
            $menuLabelParent->addChild($newMenuLabel);
        }

        return $root;
    }

    /**
     * @param array $menu
     */
    private function setMenuPrefix(array &$menu): void
    {
        /** @var Menu $value */ // return by reference - to not copy object but modify orginal instance
        $firstFloor = array_filter($menu, function (&$value) {
            if($value->getHierarchy() === 1)
                return $value;
        });

        $prefix = (new Menu())
            ->setHierarchy(1)
            ->setParent(null)
            ->setSequency(count($firstFloor));

//        /** @var Menu $lastElement */ // get last label from first floor
//        $lastElement = end($firstFloor);

        if($this->security->isGranted("ROLE_USER")) {
            //$lastElement->setCategory("Profile");
            $prefix->setCategory('Profile');
        }else{
            //$lastElement->setCategory("Sign in");
            $prefix->setCategory('Sign In');
        }

        $menu[] = $prefix;
    }

    /**
     * @param array $inputData
     * @param int $begin
     * @param int $end
     */
    private function mergeSort(array &$inputData, int $begin, int $end): void
    {
        if($begin >= $end)
        {
            return;
        }

        $mid = (int)($begin + ($end - $begin) / 2);

        $this->mergeSort($inputData, $begin, $mid);
        $this->mergeSort($inputData, $mid + 1, $end);
        $this->merge($inputData, $begin, $mid, $end);
    }

    /**
     * @param array $inputData
     * @param int $begin
     * @param int $mid
     * @param int $end
     */
    private function merge(array &$inputData, int $begin, int $mid, int $end): void
    {
        // init array
        $leftSorted = [];
        $rightSorted = [];

        $subArrayLeft = $mid - $begin + 1;
        $subArrayRight = $end - $mid;

        for($i = 0; $i < $subArrayLeft; $i++) {
            $leftSorted[$i] = $inputData[$begin + $i];
        }

        for($j=0; $j < $subArrayRight; $j++) {
            $rightSorted[$j] = $inputData[$mid + 1 + $j];
        }

        $leftArrayIndex = 0;
        $rightArrayIndex = 0;
        $sortedArrayIndex = $begin;

        // todo: don't work perfect but sorts correctly and module works :D - don't insert children after parents (using sequence)
        while($leftArrayIndex < $subArrayLeft && $rightArrayIndex < $subArrayRight) {

            /// note: sort groups but children aren't after they parent
            // 1 sort by objects hierarchy

            if($leftSorted[$leftArrayIndex]->getHierarchy() < $rightSorted[$rightArrayIndex]->getHierarchy()) {

                $inputData[$sortedArrayIndex] = $leftSorted[$leftArrayIndex];
                $leftArrayIndex++;
            } else if($leftSorted[$leftArrayIndex]->getHierarchy() > $rightSorted[$rightArrayIndex]->getHierarchy()) {

                $inputData[$sortedArrayIndex] = $rightSorted[$rightArrayIndex];
                $rightArrayIndex++;
            } else if($leftSorted[$leftArrayIndex]->getHierarchy() === $rightSorted[$rightArrayIndex]->getHierarchy()) {
                // if hierarchy is the same then sort by parents sequence

                if($leftSorted[$leftArrayIndex]->getParent() === $rightSorted[$rightArrayIndex]->getParent()) {
                    // if parent's sequences are equal or parents are the same one - sort by object sequence

                    if ($leftSorted[$leftArrayIndex]->getSequency() < $rightSorted[$rightArrayIndex]->getSequency()) {
                        $inputData[$sortedArrayIndex] = $leftSorted[$leftArrayIndex];
                        $leftArrayIndex++;
                    } else if ($leftSorted[$leftArrayIndex]->getSequency() > $rightSorted[$rightArrayIndex]->getSequency()) {
                        $inputData[$sortedArrayIndex] = $rightSorted[$rightArrayIndex];
                        $rightArrayIndex++;
                    } else {
                        dump($leftSorted[$leftArrayIndex]);
                        dump($rightSorted[$rightArrayIndex]);
                        dd('this object must be the same one');
                    }
                }else if($leftSorted[$leftArrayIndex]->getParent()->getSequency() < $rightSorted[$rightArrayIndex]->getParent()->getSequency())
                {
                    $inputData[$sortedArrayIndex] = $leftSorted[$leftArrayIndex];
                    $leftArrayIndex++;
                }else if($leftSorted[$leftArrayIndex]->getParent()->getSequency() > $rightSorted[$rightArrayIndex]->getParent()->getSequency())
                {
                    $inputData[$sortedArrayIndex] = $rightSorted[$rightArrayIndex];
                    $rightArrayIndex++;
                }
            }else{
                dump('else 1 - what the hell...');
                dump($inputData);
                dump($leftSorted[$leftArrayIndex]);
                dd($rightSorted[$rightArrayIndex]);
            }

            $sortedArrayIndex++;
        }

        // Copy the remaining elements of
        // left[], if there are any
        while ($leftArrayIndex < $subArrayLeft) {
            $inputData[$sortedArrayIndex] = $leftSorted[$leftArrayIndex];
            $sortedArrayIndex++;
            $leftArrayIndex++;
        }
        // Copy the remaining elements of
        // right[], if there are any
        while ($rightArrayIndex < $subArrayRight) {
            $inputData[$sortedArrayIndex] = $rightSorted[$rightArrayIndex];
            $sortedArrayIndex++;
            $rightArrayIndex++;
        }
    }

    /**
     * Return menu label instance
     *
     * @param ItemInterface $core
     * @param Menu|null $currentMenuLabel
     * @return ItemInterface
     */
    private function getMenuLabelParent(ItemInterface $core, ?Menu $currentMenuLabel): ItemInterface
    {
        if($currentMenuLabel === null) {
            return $core;
        }

        $menuLabel = $this->getMenuLabelParent($core, $currentMenuLabel->getParent());
        return $menuLabel[$currentMenuLabel->getCategory()] ?? $menuLabel;
    }

    /**
     * @param ItemInterface $item
     * @param Menu $label
     */
    private function styleMenuLabelParent(ItemInterface $item, Menu $label): void
    {
        // add dropdown css property to parent label
        ($item->getParent() !== null && $item->getParent()->getName() !== 'root')
            ? $item->setAttribute('class', 'dropdown-item dropdown-submenu')
            : $item->setAttribute('class', 'nav-item dropdown mx-2');   // </li>

        // set link attributes
        $linkAttributes = [
            'class' => 'nav-link dropdown-toggle',
            'id' => sprintf("%s", $this->makeSlug($label->getParent() ? $label->getParent()->getCategory(): $label->getCategory())),
            'role' => 'button',
            'data-toggle' => 'dropdown',
            'aria-haspopup' => true,
            'aria-expanded' => false
        ];

        foreach ($linkAttributes as $attributeKey => $attributeValue)
        {
            $item->setLinkAttribute($attributeKey, $attributeValue);
        }

        // set link's children attributes
        $linkChildrenAttributes = [
            'class' => 'dropdown-menu', // eg <ul>
            'aria-labelledby' => sprintf("%s", $this->makeSlug($label->getCategory()))  // add sub menu style to new label
        ];

        foreach ($linkChildrenAttributes as $attributeKey => $attributeValue)
        {
            $item->setChildrenAttribute($attributeKey, $attributeValue);
        }
    }

    /**
     * Generate route depend on menu label and its hierarchy
     *
     * @param Menu $menuElement
     * @return string
     */
    private function generateRoute(Menu $menuElement): string
    {
        if($menuElement->getParent() === null) {
            return
                'app_' . str_replace('-', '_', $this->makeSlug($menuElement->getCategory()));
        }

        return
            str_replace([' ', '-'], '_',
                $this->generateRoute($menuElement->getParent()) . ' ' . $this->makeSlug($menuElement->getCategory())
            );
    }

    /**
     * Return generated slug based on label
     *
     * @param string $label
     * @return string
     */
    private function makeSlug(string $label): string {
        return strtolower(str_replace(' ', '-', $label));
    }
}

/*
 * to fix template rendering problem:
 * https://github.com/KnpLabs/KnpMenu/issues/283
 * */