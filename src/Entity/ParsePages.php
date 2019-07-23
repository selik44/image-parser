<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParsePagesRepository")
 */
class ParsePages
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="integer")
     */
    private $count_images;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $processing_speed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setProcessingSpeed($speed)
    {
        $this->processing_speed = $speed;
    }

    public function getProcessingSpeed()
    {
        return $this->processing_speed;
    }

    public function setCountImages($count_images)
    {
        $this->count_images = $count_images;
    }

    public function getCountImages()
    {
        return $this->count_images;
    }
}
