<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
#use Doctrine\Persistence\ManagerRegistry;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadMainCategoriesData($manager);
        $this->loadElectronics($manager);
        $this->loadComputers($manager);
        $this->loadLaptops($manager);
        $this->loadBooks($manager);
        $this->loadMovies($manager);
        $this->loadRomance($manager);
    }

    private function loadElectronics($manager)
    {
        $this->loadSubCategories($manager, 'Electronics', 1);
    }

    private function loadComputers($manager)
    {
        $this->loadSubCategories($manager, 'Computers', 6);
    }

    private function loadLaptops($manager)
    {
        $this->loadSubcategories($manager,'Laptops',8);
    }
    
    private function loadBooks($manager)
    {
        $this->loadSubcategories($manager,'Books',3);
    }
    
    private function loadMovies($manager)
    {
        $this->loadSubcategories($manager,'Movies',4);
    }
    
    private function loadRomance($manager)
    {
        $this->loadSubcategories($manager,'Romance',18);
    }

    private function loadMainCategoriesData($manager)
    {
        foreach($this->getMainCategoriesData() as [$name]){
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
        }

        $manager->flush();
    }

    private function loadSubCategories($manager, $category, $parent_id)
    {
        $repository = $manager->getRepository(Category::class);
        $methodName="get{$category}Data";
        foreach($this->$methodName() as [$name]){
            $parent = $repository->find($parent_id);
            $category = new Category();
            $category->setName($name);
            $category->setParent($parent);
            $manager->persist($category);
        }

        $manager->flush();
    }

    private function getMainCategoriesData()
    {
        return [
            ['Electronics', '1'],
            ['Toys', '2'],
            ['Books', '3'],
            ['Movies', '4'],
        ];
    }

    private function getElectronicsData()
    {
        return [
            ['Cameras', '5'],
            ['Computers', '6'],
            ['Cell Phones', '7'],
        ];
    }

    private function getComputersData()
    {
        return [
            ['Laptops', 8],
            ['Desktops', 9]
        ];
    }

    private function getLaptopsData()
    {
        return [

            ['Apple',10],
            ['Asus',11], 
            ['Dell',12], 
            ['Lenovo',13], 
            ['HP',14]

        ];
    }


    private function getBooksData()
    {
        return [
            ['Children\'s Books',15],
            ['Kindle eBooks',16], 
        ];
    }


    private function getMoviesData()
    {
        return [
            ['Family',17],
            ['Romance',18], 
        ];
    }


    private function getRomanceData()
    {
        return [
            ['Romantic Comedy',19],
            ['Romantic Drama',20], 
        ];
    }
}
